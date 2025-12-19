<p align="center">
  <img src="./assets/Logo.svg" alt="QRush Logo" height="100">
</p>

## 📌 Overview
The Ordering Management System, combined with a Point-of-Sale (POS) module, aims to create a faster, more organized, and error-free workflow. Through automation, the system will remove unnecessary manual steps and improve how staff and customers interact with the ordering and billing process.
The system will also unify all ordering stations including the dine-in area, cashier station, and barbecue station into one digitally connected process. This ensures orders are properly tracked, efficiently transmitted, and accurately billed, improving overall service quality for customers and operational efficiency for staff.

## 🎯 Features
The project aims to deliver a complete Ordering Management System focused on automation and seamless process integration. The scope includes the development of:
- A QR-based Ordering Web Application for customers, allowing each dine-in table to place orders directly.
- A Point-of-Sale (POS) System for cashier staff, enabling efficient billing, payment processing, and management of take-out orders.
- A connected and streamlined ordering workflow between customers, cashiers, and kitchen staff.
The system will focus on improving accuracy, speed, and coordination across all ordering-related operations.

## 📄 Documentation
### Kitchen Display System
the KDS sending such API responses in this sense:

- #### Queued Orders 
These are the list of order that is currently confirmed or accepted by the cashier first before transferring the order to the kitchen as such this reponse shows on what the UI May expect:

```json

{
  "orders": [
    {
      "order_id": number,
      "table_id": number,
      "status": "confirmed | preparing",
      "created_at": "ISO8601",
      "order_items": [
        {
          "menu_item_id": number,
          "name": string,
          "quantity": number
        }
      ]
    }
  ]
}
```
- #### Order Status Lifecycle
The order status may not start first in the KDS but the flow will go though the KDS screen, the flow implemented as follows:

```json
{
    pending -> confirm -> preparing -> ready -> served
}
```
the order status may not revert as the flow strictly straight and the process of the order is also ongoing; once the order is needed to be cancelled it must be done before the order may enter preparing or confirmed 
