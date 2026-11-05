# AI Prompt Log

This directory contains screenshots of the frontend development prompts used during the development of the Store Order & Inventory Mini-System.

AI-assisted tools were used to prepare, refine, and implement frontend requirements, improve the user interface, debug issues, and make iterative changes based on the application's requirements.

## AI Tools Used

### 1. ChatGPT

Used for:

- Preparing and refining frontend UI development prompts.
- Converting UI requirements into clear and structured implementation instructions.
- Improving prompts based on the generated frontend output.
- Reviewing UI behaviour and suggesting necessary refinements.

### 2. Kilo Code AI (VS Code Extension)

Used for:

- Implementing the frontend billing interface using Laravel Blade, Bootstrap, and JavaScript.
- Developing customer and product interactions.
- Implementing the customer order history interface.
- Improving the UI based on iterative requirements.
- Debugging frontend issues and making UI refinements.
- Assistance with Git and GitHub commands.

## Prompt Documentation

The following six prompts represent the main stages of frontend development. Related UI improvements and refinements have been grouped under their corresponding implementation prompts.

### Phase 1: Billing Interface Development

**01. Retail Billing Interface – Development and UI Refinements**

Preparing and implementing the retail billing page, covering customer selection, product management, dynamic table rows, quantity handling, tax calculations, low-stock alerts, bill preview, and order submission.

Also includes related UI refinements such as customer name and email input presentation, search placeholders, Add Product button sizing, table input borders, column naming, section icons, button alignment, toast duration, and table footer alignment.

**02. Customer-First Workflow, Validation and Toast Notifications**

Refining the billing workflow so that product details, low-stock alerts, bill preview, and order generation remain hidden until a customer is successfully selected or created.

Also includes customer name and email validation messages and the positioning of compact success and error toast notifications in the top-right corner.

**03. Successful Order Creation and Form Reset**

Defining the expected behaviour after an order is successfully created, including clearing customer details, resetting product rows, hiding billing sections, clearing calculations, and preparing the interface for the next customer.

### Phase 2: Customer Order History

**04. Customer Order History – Initial Implementation**

Planning and implementing the customer order history feature, including customer search, order listing, order details, API integration, and navigation. The initial approach used Bootstrap modals.

**05. Customer Order History – Separate Pages and UI Improvements**

Refining the initial approach by replacing modal-based order details with dedicated Blade pages.

Also includes improvements to the order history table, customer information display, date formatting, navigation labels, customer privacy, route model binding, historical order values, and customer-order ownership validation.

### Phase 3: Frontend Refinements and Validation

**06. Low Stock Alerts – API and UI Fix**

Investigating and fixing the Low Stock Alerts section, which was incorrectly displaying an empty state despite products having low stock.

Refining the threshold condition to include products with stock less than or equal to 5, verifying API response handling and frontend rendering, and refreshing the alert list after successful order creation.

## Screenshot Naming Convention

Screenshots are organized according to their corresponding prompt numbers to make them easy to identify and review.

For prompts that require multiple screenshots, sequential numbering is used.

Example:

```text
01-billing-interface-01.png
01-billing-interface-02.png
01-billing-interface-03.png
01-billing-interface-04.png
01-billing-interface-05.png
01-billing-interface-06.png
01-billing-interface-07.png
01-billing-interface-08.png
01-billing-interface-09.png
01-billing-interface-10.png
01-billing-interface-11.png
01-billing-interface-12.png
01-billing-interface-13.png

02-customer-first-workflow-01.png
02-customer-first-workflow-02.png
02-customer-first-workflow-03.png

03-order-creation-form-reset-01.png

04-customer-order-history-01.png

05-separate-order-details-page-01.png
05-separate-order-details-page-02.png
05-separate-order-details-page-03.png
05-separate-order-details-page-04.png

06-low-stock-alerts-fix-01.png
06-low-stock-alerts-fix-02.png
06-low-stock-alerts-fix-03.png
06-low-stock-alerts-fix-04.png
```

## Note

The screenshots document the enhanced frontend UI prompts submitted to Kilo Code AI. These prompts were initially prepared and refined with ChatGPT before being used for frontend implementation.

Related refinement requests have been grouped with their corresponding main prompts to keep the documentation concise while reflecting the iterative development process, including initial implementation, requirement changes, UI improvements, and debugging.