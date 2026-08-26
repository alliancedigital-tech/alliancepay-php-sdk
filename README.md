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

> **A2A платіж:** Якщо `hppPayType` = `A2A`, необхідно змінити наступні параметри:
> - `'directType'` => `'BANK_LINK'` *(для hppPayType PURCHASE використовується `'REDIRECT'`)*
> - `'priorityBankCode'` => `'ALL_BANKS'`
> - `'merchantComment'` => `'Comment for merchant'` *(обов'язково для A2A)*
> - **ВАЖЛИВО:** A2A платежі підтримують **тільки UAH (980)**. Валюти USD та EUR не підтримуються для цього типу операцій.

### 2.1. Створення замовлення з валютами USD та EUR

SDK підтримує створення замовлень у валютах **USD (840)** та **EUR (978)**.

**ВАЖЛИВІ ОБМЕЖЕННЯ:**
- Операції **REFUND** та **COMPLETION** приймають `coinAmount` **тільки в UAH** (копійках)
- Тип операції **A2A** (`hppPayType`) **не підтримує** USD та EUR, тільки UAH

#### Приклад створення замовлення в USD:

```PHP
use AlliancePay\Sdk\Payment\Dto\Order\OrderRequestDTO;
use AlliancePay\Sdk\Payment\Order\CreateOrder;
use AlliancePay\Sdk\Services\RequestIdentification\GenerateRequestIdentification;

// Замовлення в доларах США
$orderData = [
    'merchantRequestId' => GenerateRequestIdentification::generateRequestId(),
    'merchantId' => $authDto->getMerchantId(),
    'hppPayType' => 'PURCHASE',
    'coinAmount' => 1000, // $10.00 USD (в центах)
    'currencyCode' => OrderRequestDTO::CURRENCY_USD, // 840
    'paymentMethods' => ['CARD', 'APPLE_PAY', 'GOOGLE_PAY'],
    'successUrl' => 'https://your-site.com/success',
    'failUrl' => 'https://your-site.com/fail',
    'statusPageType' => 'STATUS_TIMER_PAGE',
    'customerData' => ['senderCustomerId' => 'customer_id_1'],
];

$orderRequest = OrderRequestDTO::fromArray($orderData);
$orderService = new CreateOrder();

try {
    $response = $orderService->createOrder($orderRequest, $authDto);
    // Переадресовуйте користувача на $response->getRedirectUrl()
} catch (\AlliancePay\Sdk\Exceptions\CreateOrderException $exception) {
    // Обробка помилки
}
```

#### Приклад створення замовлення в EUR:

```PHP
use AlliancePay\Sdk\Payment\Dto\Order\OrderRequestDTO;

// Замовлення в євро
$orderData = [
    'merchantRequestId' => GenerateRequestIdentification::generateRequestId(),
    'merchantId' => $authDto->getMerchantId(),
    'hppPayType' => 'PURCHASE',
    'coinAmount' => 2500, // €25.00 EUR (в центах)
    'currencyCode' => OrderRequestDTO::CURRENCY_EUR, // 978
    'paymentMethods' => ['CARD'],
    'successUrl' => 'https://your-site.com/success',
    'failUrl' => 'https://your-site.com/fail',
    'statusPageType' => 'STATUS_TIMER_PAGE',
    'customerData' => ['senderCustomerId' => 'customer_id_1'],
];

$orderRequest = OrderRequestDTO::fromArray($orderData);
// ... далі так само як в попередньому прикладі
```

#### Використання CoinAmountConverter для конвертації валют:

Якщо вам потрібно конвертувати суму з іноземної валюти в UAH (наприклад, для операцій REFUND або COMPLETION), використовуйте trait `CoinAmountConverter`:

```PHP
use AlliancePay\Sdk\Traits\CoinAmountConverter;

// Приклад: конвертація $10.00 USD в UAH копійки
// Якщо курс USD/UAH = 39.50
$uahKopecks = CoinAmountConverter::convertToUahCoins(
    amount: 10.00,          // сума в USD
    conversionRate: 39.50   // курс: 1 USD = 39.50 UAH
);
// $uahKopecks === 39500 (395.00 UAH в копійках)

// Отримання коду валюти за її літерним позначенням
$currencyCode = CoinAmountConverter::currencyCodeFromAlpha('USD');
// $currencyCode === 840

// Підтримувані валюти
OrderRequestDTO::CURRENCY_UAH; // 980
OrderRequestDTO::CURRENCY_USD; // 840
OrderRequestDTO::CURRENCY_EUR; // 978
```

#### Callback для замовлень з іноземними валютами:

Коли замовлення створене в USD або EUR, callback містить додаткові поля з інформацією про конвертацію:

```PHP
use AlliancePay\Sdk\Payment\Callback\CallbackHandler;

$callbackHandler = new CallbackHandler();
$callback = $callbackHandler->handle($authDto);

// Отримуємо операцію з callback
$operation = $callback->getOperation(); // OperationPurchaseDTO

// Якщо замовлення було в іноземній валюті, ці поля будуть заповнені:
$sourceAmount = $operation->getSourceAmount();         // Сума в оригінальній валюті (центи)
$sourceCurrency = $operation->getSourceCurrencyCode(); // Код валюти (840 = USD, 978 = EUR)
$conversionRate = $operation->getConversionRate();     // Курс конвертації в UAH

// coinAmount завжди в UAH копійках
$uahAmount = $operation->getCoinAmount();
```
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

> **ВАЖЛИВО:** Незалежно від валюти оригінального замовлення (UAH, USD або EUR), поле `coinAmount` в операції REFUND **завжди має бути вказане в UAH (копійках)**.
>
> Якщо оригінальне замовлення було створене в USD або EUR, вам необхідно:
> 1. Отримати дані про конвертацію з callback (поля `sourceAmount`, `sourceCurrencyCode`, `conversionRate`)
> 2. Використати `CoinAmountConverter::convertToUahCoins()` для конвертації суми повернення в UAH копійки

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
    'coinAmount' => 100, // Сума повернення в UAH копійках
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

### Приклад Refund для замовлення в іноземній валюті:

Якщо оригінальне замовлення було створене в USD або EUR, вам потрібно конвертувати суму повернення в UAH:

```PHP
use AlliancePay\Sdk\Traits\CoinAmountConverter;
use AlliancePay\Sdk\Payment\Refund\RefundOrder;
use AlliancePay\Sdk\Payment\Dto\Refund\RefundRequestDTO;
use AlliancePay\Sdk\Services\DateTime\DateTimeImmutableProvider;
use AlliancePay\Sdk\Services\RequestIdentification\GenerateRequestIdentification;

// Припустимо, оригінальне замовлення було на $10.00 USD
// і з callback ви отримали conversionRate = 39.50 (1 USD = 39.50 UAH)

// Конвертуємо суму повернення в UAH копійки
$refundAmountUSD = 10.00;        // Повертаємо $10.00
$conversionRate = 39.50;         // Курс з callback
$refundAmountUAH = CoinAmountConverter::convertToUahCoins($refundAmountUSD, $conversionRate);
// $refundAmountUAH = 39500 (395.00 UAH в копійках)

$refundData = [
    'merchantRequestId' => GenerateRequestIdentification::generateRequestId(),
    'merchantId' => $authDto->getMerchantId(),
    'operationId' => 'OPERATION_ID',
    'coinAmount' => $refundAmountUAH, // Сума в UAH копійках!
    'date' => DateTimeImmutableProvider::fromString('now', DateTimeImmutableProvider::KYIV_TIMEZONE),
];

$refundDto = RefundRequestDTO::fromArray($refundData);
$refundService = new RefundOrder();

try {
    $resultRefund = $refundService->createRefund($refundDto, $authDto);
} catch (\AlliancePay\Sdk\Exceptions\RefundOrderException $exception) {
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
___

## 6. PREAUTH (Попередня авторизація)

PREAUTH — двофазний платіж: спочатку кошти блокуються на картці покупця (фаза 1), а потім мерчант підтверджує списання через операцію [COMPLETION](#7-completion-завершення-попередньої-авторизації) (фаза 2).

Для створення PREAUTH-замовлення використовуються ті самі класи, що й для звичайного PURCHASE — лише `hppPayType` змінюється на `'PREAUTH'`.

### Поле `preAuthExpDate` — термін дії блокування

| Сценарій | Поведінка |
|---|---|
| Поле **не передано** | Генерується автоматично: `now + 2 години` |
| Поле **передано явно** | Має бути в діапазоні від `now + 2 год` до `now + 28 діб` |

**Формат:** `YYYY-MM-DD HH:MM:SS.ss±HH:MM` *(наприклад: `2026-08-10 15:30:00.00+03:00`)*

### Приклад створення PREAUTH-замовлення:

```PHP
use AlliancePay\Sdk\Payment\Order\CreateOrder;
use AlliancePay\Sdk\Payment\Dto\Order\OrderRequestDTO;
use AlliancePay\Sdk\Services\RequestIdentification\GenerateRequestIdentification;
use AlliancePay\Sdk\Services\DateTime\DateTimeImmutableProvider;

// 1. Готуємо дані замовлення
$orderData = [
    'merchantRequestId' => GenerateRequestIdentification::generateRequestId(),
    'merchantId'        => $authDto->getMerchantId(),
    'hppPayType'        => 'PREAUTH',
    'directType'        => 'REDIRECT',
    'coinAmount'        => 10050,
    'paymentMethods'    => ['CARD'],
    'successUrl'        => 'https://your-site.com/success',
    'failUrl'           => 'https://your-site.com/fail',
    'statusPageType'    => 'STATUS_TIMER_PAGE',
    'customerData'      => ['senderCustomerId' => 'customer_id_1'],
    // Опціонально: явно вказати термін дії блокування (від +2 год до +28 діб від поточного часу).
    // Якщо не передати — SDK встановить автоматично now + 2 години.
    // 'preAuthExpDate' => '2026-08-10 15:30:00.00+03:00',
];

// 2. Створюємо DTO та виконуємо запит
$createOrder  = new CreateOrder();
$orderRequest = OrderRequestDTO::fromArray($orderData);

try {
    $response      = $createOrder->createOrder($orderRequest, $authDto);
    $responseArray = $response->toArray();

    // ВАЖЛИВО: зберегти ці значення — вони знадобляться для операції COMPLETION
    $hppOrderId  = $responseArray['hppOrderId'];   // ідентифікатор замовлення
    $redirectUrl = $responseArray['redirectUrl'];   // URL для перенаправлення покупця

} catch (\AlliancePay\Sdk\Exceptions\CreateOrderException $e) {
    echo "Помилка створення PREAUTH: " . $e->getMessage();
} catch (\AlliancePay\Sdk\Exceptions\ValidateDataException $e) {
    echo "Помилка валідації: " . $e->getMessage();
}
```

> **Важливо:** Після успішного створення PREAUTH-замовлення збережіть у вашій системі `hppOrderId`, `operationId` та `coinAmount` з відповіді — вони будуть потрібні для виконання операції COMPLETION.
___

## 7. COMPLETION (Завершення попередньої авторизації)

COMPLETION підтверджує та списує кошти, заблоковані операцією PREAUTH. Для цього використовується окремий клас `CreateOrderCompletion` та DTO `OrderRequestCompletionDTO`.

### Ключові правила

* **`originalOperationId`** — це поле `operationId` з відповіді на PREAUTH-замовлення (не `ecomOperationId`).
* **`coinAmount`** — сума списання **завжди в UAH (копійках)**. Дозволяється відхилення **±20%** від оригінальної суми PREAUTH. Наприклад, якщо PREAUTH був на `10000` копійок, то COMPLETION може бути від `8000` до `12000`.
* **Третій аргумент** методу `createCompletion()` — оригінальна сума PREAUTH (`int`, копійки в UAH). SDK використовує її для внутрішньої валідації ±20% перед надсиланням запиту.

> **ВАЖЛИВО:** Незалежно від валюти оригінального PREAUTH замовлення (UAH, USD або EUR), поле `coinAmount` в операції COMPLETION **завжди має бути вказане в UAH (копійках)**.
>
> Якщо оригінальне PREAUTH замовлення було створене в USD або EUR, використовуйте `CoinAmountConverter::convertToUahCoins()` для конвертації суми в UAH копійки.

### Поля `OrderRequestCompletionDTO`

| Поле | Тип | Опис |
|---|---|---|
| `merchantRequestId` | string | Унікальний ID запиту (генерується для кожного COMPLETION) |
| `merchantId` | string | Ідентифікатор мерчанта |
| `originalOperationId` | string | `operationId` з відповіді на PREAUTH |
| `coinAmount` | int | Сума списання в копійках (±20% від суми PREAUTH) |
| `date` | DateTimeImmutable | Дата/час виконання запиту |
| `notificationUrl` | string | URL для отримання webhook-сповіщення про результат |

### Приклад виконання COMPLETION:

```PHP
use AlliancePay\Sdk\Payment\Order\CreateOrderCompletion;
use AlliancePay\Sdk\Payment\Dto\Order\OrderRequestCompletionDTO;
use AlliancePay\Sdk\Services\DateTime\DateTimeImmutableProvider;
use AlliancePay\Sdk\Services\RequestIdentification\GenerateRequestIdentification;

// 1. Дані отримані з вашої БД після успішного PREAUTH
$originalOperationId = 'OPERATION_ID_FROM_PREAUTH'; // operationId з відповіді PREAUTH
$originalCoinAmount  = 10050;                         // сума оригінального PREAUTH в копійках

// 2. Готуємо дані для COMPLETION
$completionData = [
    'merchantRequestId'   => GenerateRequestIdentification::generateRequestId(),
    'merchantId'          => $authDto->getMerchantId(),
    'originalOperationId' => $originalOperationId,
    'coinAmount'          => 10050, // сума списання (±20% від $originalCoinAmount)
    'date'                => DateTimeImmutableProvider::nowByTimezone(DateTimeImmutableProvider::KYIV_TIMEZONE),
    'notificationUrl'     => 'https://your-site.com/webhook/completion',
];

// 3. Виконуємо COMPLETION
$completionDto    = OrderRequestCompletionDTO::fromArray($completionData);
$createCompletion = new CreateOrderCompletion();

try {
    /**
     * Третій аргумент — оригінальна сума PREAUTH для SDK-валідації ±20%
     * @var \AlliancePay\Sdk\Payment\Dto\Order\OrderCompletionResponseDTO $response
     */
    $response      = $createCompletion->createCompletion($completionDto, $authDto, $originalCoinAmount);
    $responseArray = $response->toArray();

    // Ключові поля відповіді:
    // $responseArray['status']              — статус операції
    // $responseArray['operationId']         — ID операції COMPLETION
    // $responseArray['rrn']                 — RRN операції COMPLETION
    // $responseArray['rrnPreauth']          — RRN оригінальної PREAUTH операції
    // $responseArray['preauthOperationId']  — operationId оригінальної PREAUTH операції
    // $responseArray['preauthCoinAmount']   — сума оригінальної PREAUTH в копійках

} catch (\AlliancePay\Sdk\Exceptions\CompletionAmountException $e) {
    // Спрацьовує якщо coinAmount виходить за межі ±20% від оригінальної суми PREAUTH
    echo "Помилка суми COMPLETION: " . $e->getMessage();
    print_r($e->getPayload()); // містить: originalCoinAmount, completionCoinAmount, allowedMin, allowedMax
} catch (\AlliancePay\Sdk\Exceptions\CreateCompletionException $e) {
    // Загальна помилка виконання COMPLETION (мережа, шифрування, API тощо)
    echo "Помилка COMPLETION: " . $e->getMessage();
    print_r($e->getPayload());
}
```