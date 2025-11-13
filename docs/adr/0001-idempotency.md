# ADR 0001 — Idempotency strategy
Date: 2025-11-13
Status: Accepted

## Context
Payments must be idempotent: the same POST /payments request (same Idempotency-Key) within 24h must not create duplicate payments.

## Decision
- Accept an `Idempotency-Key` header on POST /payments.
- Use Redis as the short-term idempotency cache. Key: `idempotency:{Idempotency-Key}`.
- On first request: run DB transaction to create Payment; store the serialized response in Redis with TTL 24h (`EX = 86400`) using `SETEX`.
- On subsequent requests (same key) within TTL: return cached response with 201 and no new DB writes.
- Ensure logic is applied in the service layer (PaymentService) so controllers remain thin.

## Consequences
- Redis outage: degrade to checking Payment repository for an existing payment by `order_id` & `reference` (best-effort) and return an error if uncertain. Documented in README.
- Long-term persistence of idempotency decisions requires an outbox or durable idempotency store; Redis is sufficient for the scope.
