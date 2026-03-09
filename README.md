# AlliancePay PHP SDK

Це офіційне PHP SDK для інтеграції з платіжними методами HPP https://docs.merchant.alb.ua/platizhni-metodi-hpp сервіса **AlliancePay**. 
SDK дозволяє легко виконувати авторизацію, створювати замовлення та керувати транзакціями.

---

## Технічні вимоги

Перед початком роботи переконайтеся, що ваше серверне середовище відповідає наступним вимогам:

* **PHP:** версія `8.0` або вище.
* **Composer:** для керування залежностями.
* **Обов'язкові розширення PHP:**
    * `sodium`
    * `hash`
    * `gmp`
    * `openssl`

---

## Встановлення

Встановіть пакет за допомогою Composer:

```bash
composer require alliancepay/payment-sdk
```

## 1. Авторизація

Для початку роботи необхідно ініціалізувати сесію авторизації. Для цього використовується клас ```AuthorizationService```.

### Приклад ініціалізації:

```PHP
use AlliancePay\Sdk\Services\Authorization\AuthorizationService;

$authService = new AuthorizationService();

$config = [
    'baseUrl' => 'https://api-ecom-prod.bankalliance.ua/', // Базовий URL сервісу надається банком
    'merchantId' => 'YOUR_MERCHANT_ID', // Надається банком
    'serviceCode' => 'YOUR_SERVICE_CODE', // Надається банком
    'authenticationKey' => 'YOUR_AUTH_KEY' // Надається банком
];

/** @var \AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO $authDto */
$authDto = $authService->initAuthorization($config);

// ВАЖЛИВО: Отриманий об'єкт $authDto необхідно зберегти у вашій базі даних.
// Він містить токени (authToken, refreshToken) та термін їх дії, 
// які будуть потрібні для всіх наступних запитів.
```
**ВАЖЛИВО:**
### Приклад збереження та відновлення обʼєкту авторизації ```AuthorizationDTO```.
````PHP
// Отримання окремих параметрів обʼєкту якщо планується зберігати як окрему сутність.
    $baseUrl = $authDto->getBaseUrl();
    $merchantId = $authDto->getMerchantId();
    $serviceCode = $authDto->getServiceCode();
    $authenticationKey = $authDto->getAuthenticationKey();
    $refreshToken = $authDto->getRefreshToken();
    $authToken = $authDto->getAuthToken();
    $deviceId = $authDto->getDeviceId();
    $serverPublic = $authDto->getServerPublic();
    $tokenExpirationDateTime = $authDto->getTokenExpirationDateTime();

// або через JSON, якщо збереження відбувається в JSON строці
$jsonAuth = json_encode($authDto->toArray()); 

// Приклад відновлення
$authDto = AuthorizationDTO::fromArray(json_decode($jsonFromDb, true));
````
### Робота з авторизаційними даними.
Якщо у вас є збережені авторизаційні дані вам потрібно відновити обʼєкт авторизації по вище описаному прикладу.
Важливо памʼятати якщо термін дії технічної сессії завершився тоді метод ```initAuthorization()``` 
оновить дані в обʼєкті (відбудеться запит і оновлення сесії) та його потрібно зберегти в своїй системі.

### Структура об'єкта AuthorizationDTO:
#### Після успішного виклику ви отримаєте об'єкт з наступними даними:

* baseUrl, merchantId, serviceCode, authenticationKey — ваші конфігураційні дані.
* authToken — токен для поточних запитів.
* refreshToken — токен для оновлення авторизації.
* deviceId, serverPublic — технічні ідентифікатори.
* tokenExpirationDateTime — об'єкт DateTimeImmutable з часом закінчення дії токена.

Збережений об'єкт ```AuthorizationDTO``` є обов'язковим аргументом для виконання інших операцій в API:
* create-order (/ecom/execute_request/hpp/v1/create-order): Створення нових платежів.
* refund (/ecom/execute_request/payments/v3/refund): Повернення коштів.
* operations (/ecom/execute_request/hpp/v1/operations): Перевірка статусу транзакцій.
___

## 2. Створення замовлення
Для створення платежу необхідно використовувати раніше збережений об'єкт AuthorizationDTO та підготувати дані замовлення через ```OrderRequestDTO```.

### Приклад коду для створення замовлення:
````PHP
use AlliancePay\Sdk\Payment\Dto\Order\OrderRequestDTO;
use AlliancePay\Sdk\Payment\Services\OrderService;
use AlliancePay\Sdk\Services\RequestIdentification\GenerateRequestIdentification;

// 1. Готуємо дані замовлення
$orderData = [
    'merchantRequestId' => GenerateRequestIdentification::generateRequestId(),
    'merchantId' => $authDto->getMerchantId(),
    'hppPayType' => 'PURCHASE',
    'coinAmount' => 10050,
    'paymentMethods' => ['CARD', 'APPLE_PAY', 'GOOGLE_PAY'],
    'successUrl' => 'https://your-site.com/success',
    'failUrl' => 'https://your-site.com/fail',
    'statusPageType' => 'STATUS_TIMER_PAGE',
    'customerData' => ['senderCustomerId' => 'customer_id_1'],
];

// 2. Створюємо DTO об'єкт
$orderRequest = OrderRequestDTO::fromArray($orderData);

// 3. Створюємо OrderService
$orderService = new OrderService();
// Створюємо замовлення
/**
 * $orderRequestDto -> AlliancePay\Sdk\Payment\Dto\Order\OrderRequestDTO
 * $authDto -> AlliancePay\Sdk\Services\Authorization\Dto\AuthorizationDTO
 */
try {
    $response = $orderService->createOrder($orderRequest, $authDto);
} catch (\AlliancePay\Sdk\Exceptions\CreateOrderException $exception) {
    // 
}
````
___

## 3. Обробка зворотних викликів (Callback/Webhook)
Для автоматичної обробки повідомлень від платіжного шлюзу (Webhooks) використовуйте ```CallbackHandler```.
Це дозволяє вашій системі дізнатися про успішну оплату в реальному часі.

#### Важливо:
Якщо ваша система має змогу отримати тіло запиту та серіалізувати JSON в масив тоді слід передавати в метод handle()
другий параметр $payload.
Якщо ні то метод handle() автоматично отримає тіло ```php://input```

````php
$bodyJson = file_get_contents('php://input');
$payload = json_decode($bodyJson, true);
````

Приклад використання:

````PHP
use AlliancePay\Sdk\Payment\Callback\CallbackHandler;
use AlliancePay\Sdk\Payment\Dto\Callback\CallbackDTO;

// Отримуємо сирі дані від AlliancePay (JSON)
$bodyJson = file_get_contents('php://input');
$payload = json_decode($bodyJson, true);

$callbackHandler = new CallbackHandler();

try {
    // Обробка вхідних даних з перевіркою підпису
    /** @var CallbackDTO $callback */
    $callback = $callbackHandler->handle($authDto, $payload);
    
    // Якщо обробка успішна, повертаємо 200 OK сервісу AlliancePay
    http_response_code(200);
    echo 'OK';
} catch (\Exception $e) {
    // Логування помилки та відповідь з помилкою
    http_response_code(400);
    echo 'Error';
}
````
___

## 4. Повернення коштів (Refund)

Для виконання операції повернення коштів використовується клас `AlliancePay\Sdk\Payment\Refund\RefundOrder`. 
Ви можете ініціювати як повне, так і часткове повернення, передавши відповідну суму в DTO.

### Приклад виконання Refund:

Для роботи знадобляться допоміжні сервіси SDK для генерації ID запиту та роботи з часом.

```PHP
use AlliancePay\Sdk\Payment\Refund\RefundOrder;
use AlliancePay\Sdk\Payment\Dto\Refund\RefundRequestDTO;
use AlliancePay\Sdk\Services\DateTime\DateTimeImmutableProvider;
use AlliancePay\Sdk\Services\RequestIdentification\GenerateRequestIdentification;

// 1. Ініціалізація сервісу повернення
$refundService = new RefundOrder();

// 2. Підготовка даних (приклад для масиву даних)
$refundData = [
    'merchantRequestId' => GenerateRequestIdentification::generateRequestId(),
    'merchantId' => $authDto->getMerchantId(),
    'operationId' => 'OPERATION_ID', // ID успішної операції по створенню замовлення.
    'coinAmount' => 100, // Сума повернення в копійках
    'date' => DateTimeImmutableProvider::fromString('now', DateTimeImmutableProvider::KYIV_TIMEZONE),
];

// 3. Виконання запитів
try {
    $refundDto = RefundRequestDTO::fromArray($refundData);
        
    // Виклик методу createRefund (потрібен збережений $authDto)
    $resultRefund = $refundService->createRefund($refundDto, $authDto);
} catch (\AlliancePay\Sdk\Exceptions\RefundOrderException $exception) {
    // Обробка помилок (наприклад, недостатньо коштів або невірний ID операції)
    echo "Помилка повернення: " . $exception->getMessage();
}
```
___

## 5. Перевірка статусу замовлення

Якщо вам потрібно вручну перевірити поточний стан транзакції 
(наприклад, за кроном або якщо користувач закрив сторінку оплати), використовуйте клас `AlliancePay\Sdk\Payment\Order\CheckOrderData`.

### Приклад перевірки статусу:

Для перевірки достатньо мати `hppOrderId` та об'єкт авторизації.

```PHP
use AlliancePay\Sdk\Payment\Order\CheckOrderData;

// 1. Ініціалізація сервісу перевірки
$checkService = new CheckOrderData();

// 2. Виклик методу перевірки
// Перший аргумент - ідентифікатор замовлення hppOrderId
// Другий аргумент - ваш збережений об'єкт AuthorizationDTO
try {
    /** @var AlliancePay\Sdk\Payment\Dto\Order\OrderDataResponseDTO $orderStatus */
    $orderStatus = $checkService->checkOrderData('HPP_ORDER_ID', $authDto);
} catch (\Exception $e) {
    echo "Помилка при перевірці статусу: " . $e->getMessage();
}
```