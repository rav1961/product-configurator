# Business Requirements

Role, produkty, konfigurator, koszyk, zamówienia, historia, PDF, powiadomienia.

## Role

System ról oparty o Spatie Permission:

- `admin` — pełny dostęp; zarządzanie systemem, użytkownikami, katalogiem, konfiguratorem, cenami i zamówieniami; dostęp do panelu Filament.
- `manager` — zarządzanie katalogiem, konfiguratorem i cenami oraz obsługa zamówień; bez ustawień systemowych i zarządzania użytkownikami; dostęp do panelu Filament.
- `sales` — obsługa zapytań ofertowych klientów, wyceny i zmiana statusów zamówień; bez edycji katalogu; dostęp do panelu Filament.
- `customer` — klient: konfigurator, koszyk, składanie zapytań ofertowych, historia zamówień; korzysta z frontendu SPA (bez panelu).

Rola domyślna po rejestracji: `customer`.