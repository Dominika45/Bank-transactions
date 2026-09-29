# Import transakcji bankowych

Aplikacja do importowania transakcji bankowych z plików **CSV, JSON i XML**.

Projekt został wykonany w oparciu o:

* **Laravel 12** – backend REST API
* **Vue 3** – frontend
* **Tailwind CSS** – stylowanie interfejsu
* **MySQL **– baza danych
* **Vite** – budowanie frontendu

## Funkcjonalności

Aplikacja umożliwia:

* przesłanie pliku CSV, JSON lub XML,
* walidację każdej transakcji,
* zapis poprawnych transakcji w bazie danych,
* zapis błędnych rekordów w logach importu,
* zapis informacji o każdym imporcie,
* wyświetlenie listy wykonanych importów,
* wyświetlenie szczegółów wybranego importu,
* wyświetlenie komunikatów błędów dla niepoprawnych rekordów,
* automatyczne odświeżenie listy importów po wykonaniu importu.

## Architektura

### Backend

Backend został podzielony na kilka odpowiedzialności.

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── ImportController.php
│   └── Requests/
│       └── ImportRequest.php
│
├── Import/
│   ├── Contracts/
│   │   └── ImportParser.php
│   ├── Parsers/
│   │   ├── CsvParser.php
│   │   ├── JsonParser.php
│   │   └── XmlParser.php
│   ├── ImportParserFactory.php
│   └── TransactionValidator.php
│
├── Models/
│   ├── Import.php
│   ├── ImportLog.php
│   └── Transaction.php
│
└── Services/
    └── ImportService.php
```

### Przepływ importu

```text
POST /api/imports
        │
        ▼
ImportController
        │
        ▼
ImportService
        │
        ▼
ImportParserFactory
        │
        ├── CSV → CsvParser
        ├── JSON → JsonParser
        └── XML → XmlParser
        │
        ▼
Ujednolicone rekordy
        │
        ▼
TransactionValidator
        │
        ├── poprawny rekord
        │       ↓
        │   transactions
        │
        └── błędny rekord
                ↓
            import_logs
```

Dzięki zastosowaniu wspólnego interfejsu `ImportParser` dodanie kolejnego formatu pliku nie wymaga zmiany logiki znajdującej się w `ImportService`.

## Walidacja

Każdy rekord jest walidowany niezależnie.

Obowiązujące reguły:

| Pole               | Walidacja                           |
| ------------------ | ----------------------------------- |
| `transaction_id`   | wymagane                            |
| `account_number`   | wymagane, poprawny IBAN             |
| `transaction_date` | wymagana poprawna data              |
| `amount`           | wymagana liczba większa od 0        |
| `currency`         | wymagane dokładnie 3 wielkie litery |

Niepoprawne rekordy nie są zapisywane w tabeli `transactions`.

Zamiast tego do `import_logs` trafiają:

* identyfikator importu,
* `transaction_id`,
* komunikat błędu.

## Status importu

Import może otrzymać jeden z trzech statusów:

| Status    | Znaczenie                                                     |
| --------- | ------------------------------------------------------------- |
| `success` | wszystkie rekordy zostały poprawnie zaimportowane             |
| `partial` | część rekordów została zaimportowana, a część zawierała błędy |
| `failed`  | żaden rekord nie został poprawnie zaimportowany               |

Przykład:

```text
100 rekordów
97 poprawnych
3 błędne

status = partial
```

## Struktura bazy danych

### `transactions`

Przechowuje poprawnie zaimportowane transakcje.

```text
id
transaction_id
account_number
transaction_date
amount
currency
created_at
```

### `imports`

Przechowuje informacje o wykonanych importach.

```text
id
file_name
total_records
successful_records
failed_records
status
created_at
```

### `import_logs`

Przechowuje błędy dotyczące poszczególnych rekordów.

```text
id
import_id
transaction_id
error_message
created_at
```

## API

### POST `/api/imports`

Importuje plik.

Request powinien być wysłany jako `multipart/form-data`.

Pole:

```text
import_file
```

Obsługiwane formaty:

```text
CSV
JSON
XML
```

Przykładowa odpowiedź:

```json
{
    "id": 1,
    "file_name": "transactions.csv",
    "total_records": 10,
    "successful_records": 8,
    "failed_records": 2,
    "status": "partial"
}
```

### GET `/api/imports`

Zwraca listę wykonanych importów.

Przykładowa odpowiedź:

```json
[
    {
        "id": 1,
        "file_name": "transactions.csv",
        "total_records": 10,
        "successful_records": 8,
        "failed_records": 2,
        "status": "partial"
    }
]
```

### GET `/api/imports/{id}`

Zwraca szczegóły wybranego importu wraz z logami błędów.

Przykładowa odpowiedź:

```json
{
    "id": 1,
    "file_name": "transactions.csv",
    "total_records": 10,
    "successful_records": 8,
    "failed_records": 2,
    "status": "partial",
    "importLog": [
        {
            "id": 1,
            "import_id": 1,
            "transaction_id": "TX003",
            "error_message": "Pole kwota musi być większe od 0."
        }
    ]
}
```

## Frontend

Frontend został wykonany w Vue 3.

Struktura:

```text
resources/js/
├── app.js
├── App.vue
│
├── router/
│   └── index.js
│
├── views/
│   └── Imports/
│       ├── ImportsIndex.vue
│       └── ImportDetails.vue
│
├── components/
│   ├── Navigation.vue
│   ├── ImportsList.vue
│   └── ImportUploadModal.vue
│
└── services/
    └── importService.js
```

### Widok listy importów

Dostępny pod:

```text
/imports
```

Wyświetla:

* nazwę pliku,
* liczbę wszystkich rekordów,
* liczbę poprawnych rekordów,
* liczbę błędnych rekordów,
* status importu,
* datę dodania,
* link do szczegółów.

Import pliku odbywa się za pomocą formularza w modalu.

Po poprawnym imporcie lista zostaje automatycznie odświeżona.

### Szczegóły importu

Dostępne pod:

```text
/imports/{id}
```

Widok prezentuje informacje o imporcie oraz tabelę błędnych rekordów.

Dla każdego błędu wyświetlane są:

* `transaction_id`,
* komunikat błędu.

## Wymagania

Do uruchomienia projektu potrzebne są:

* PHP 8.2+
* Composer
* Node.js
* npm
* baza danych obsługiwana przez Laravel

## Instalacja

### 1. Pobranie projektu

```bash
git clone <adres-repozytorium>
cd bank-transactions
```

### 2. Instalacja zależności PHP

```bash
composer install
```

### 3. Instalacja zależności JavaScript

```bash
npm install
```

### 4. Konfiguracja środowiska

Utwórz plik `.env` na podstawie `.env.example`.

```bash
cp .env.example .env
```

Następnie skonfiguruj połączenie z bazą danych w `.env`.

### 6. Migracje

```bash
php artisan migrate
```

### 7. Uruchomienie backendu

```bash
php artisan serve
```

Backend będzie dostępny pod:

```text
http://127.0.0.1:8000
```

### 8. Uruchomienie frontendu

W drugim terminalu:

```bash
npm run dev
```

Następnie aplikacja będzie dostępna pod adresem wyświetlonym przez Vite.

## Testy

Testy można uruchomić poleceniem:

```bash
php artisan test
```

## Przykładowy plik CSV

```csv
transaction_id,account_number,transaction_date,amount,currency
TX001,PL61109010140000071219812874,2026-09-01,100.00,PLN
TX002,PL61109010140000071219812874,2026-09-02,250.50,PLN
```

Separatorem dla plików CSV jest przecinek.

## Obsługa błędnych rekordów

Przykładowy niepoprawny rekord:

```csv
TX003,PL61109010140000071219812874,2026-09-03,-10.00,PLN
```

Ponieważ kwota musi być większa od `0`, rekord nie zostanie zapisany w `transactions`.

Zostanie natomiast utworzony wpis w `import_logs`:

```text
transaction_id: TX003
error_message: Pole kwota musi być większe od 0.
```

Pozostałe poprawne rekordy z tego samego pliku zostaną zaimportowane.

## Decyzje projektowe

### Osobne parsery dla formatów

CSV, JSON i XML mają różną strukturę, dlatego każdy format posiada własny parser implementujący wspólny interfejs `ImportParser`.

Dzięki temu `ImportService` nie musi wiedzieć, w jaki sposób konkretny format jest odczytywany.

### Factory

`ImportParserFactory` wybiera parser na podstawie rozszerzenia pliku.

```text
csv → CsvParser
json → JsonParser
xml → XmlParser
```

### Walidacja na dwóch poziomach

Walidacja pliku wykonywana jest na poziomie requestu, natomiast walidacja poszczególnych rekordów wykonywana jest podczas importu.

Dzięki temu pojedynczy błędny rekord nie blokuje całego importu.

### Logowanie błędów

Błędne rekordy nie są zapisywane jako transakcje. Ich błędy są zapisywane w `import_logs`, dzięki czemu użytkownik może sprawdzić, które rekordy wymagały poprawy.

Obecna implementacja skupia się na wymaganiach określonych w zadaniu.
