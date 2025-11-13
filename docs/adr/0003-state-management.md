# ADR 0003 — State machine choices for orders & payments
Date: 2025-11-13
Status: Accepted

## Context
Orders and payments have clearly defined states.

## Decision
- Orders: ENUM (`pending`, `confirmed`, `fulfilled`, `cancelled`).
- Payments: ENUM (`authorized`, `captured`, `voided`, `refunded`).
- State transitions are enforced in service layer (PaymentService/OrderService) with ValidationExceptions for invalid transitions.
- Use DB-level constraints (ENUM) plus application checks to prevent invalid transitions.

## Consequences
- Easier to reason about transitions and reconciliation.
- If states need to change in future, create migration + ADR to record reasoning.
