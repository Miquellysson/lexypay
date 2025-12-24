# LEXI Gateway MVP (Laravel 11)

MVP de gateway + painel para merchants, com Stripe Checkout e webhooks idempotentes.

## Requisitos

- PHP 8.2+ (tested with 8.4)
- Composer
- Banco: SQLite/MySQL/Postgres

## Setup rapido

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Painel Filament: `http://localhost:8000/admin`

## Seed demo

O seeder cria:

- 1 merchant: "Demo Merchant"
- 1 user admin: demo@demo.com / password
- 50 payments fake (USD) nos ultimos 14 dias

Ao rodar `php artisan migrate --seed`, o console imprime o API key (somente uma vez).

## Variaveis de ambiente

```
STRIPE_SECRET_KEY=
STRIPE_WEBHOOK_SECRET=
CHECKOUT_SUCCESS_URL=https://shop.example.com/checkout/status/{PAYMENT_ID}
CHECKOUT_CANCEL_URL=https://shop.example.com/checkout/status/{PAYMENT_ID}
INTERNAL_SECRET=internal-secret
```

## API (Gateway)

Autenticacao via `Authorization: Bearer <api_key>`.

### Criar pagamento

```bash
curl -X POST http://localhost:8000/api/v1/payments \
  -H "Authorization: Bearer <API_KEY>" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 1500,
    "currency": "USD",
    "provider": "stripe",
    "idempotency_key": "order_123",
    "metadata": { "order_id": "order_123" }
  }'
```

### Consultar pagamento

```bash
curl http://localhost:8000/api/v1/payments/{payment_id} \
  -H "Authorization: Bearer <API_KEY>"
```

## Webhook Stripe

Endpoint: `POST /api/v1/webhooks/stripe`

Para testar localmente, use o Stripe CLI (ou exponha a aplicacao via URL publica):

```bash
stripe listen --forward-to http://localhost:8000/api/v1/webhooks/stripe
```

## Endpoint interno (dev)

```bash
curl -X POST http://localhost:8000/api/internal/payments/{id}/mark-paid \
  -H "X-Internal-Secret: <INTERNAL_SECRET>"
```

## Testes

```bash
php artisan test
```
