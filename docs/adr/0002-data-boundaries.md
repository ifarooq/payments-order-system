# ADR 0002 — Data boundaries & ownership
Date: 2025-11-13
Status: Accepted

## Context
We need clear ownership of data models and cross-service boundaries for payments, orders and refunds.

## Decision
- Single bounded context: `orders` own order lifecycle & items.
- `payments` are a separate aggregate referencing orders via `order_id`. Payments own their own lifecycle and timestamps.
- `refunds` reference payments via `payment_id`.
- Reconciliation uses read-only daily totals aggregated from payments; these are built from payments table with indexes on `status` and `created_at`.

## Consequences
- Cross-aggregate consistency is handled with DB transactions and queue-based eventually-consistent operations when needed.
- Avoid joins that cause N+1 by using eager loading in repositories (e.g., `with('items.product')`).
