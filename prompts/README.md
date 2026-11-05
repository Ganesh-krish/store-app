
# AI Prompt Log

This directory contains screenshots of the actual prompts used during the development of the Store Order & Inventory Mini-System.

AI-assisted tools were used at different stages of development for requirement analysis, project planning, implementation guidance, frontend development, debugging, UI improvements, and Git-related assistance.

## AI Tools Used

### 1. ChatGPT

Used for:

- Understanding and analyzing the assignment requirements.
- Planning the project structure and development workflow.
- Database schema design and backend implementation guidance.
- REST API development and debugging.
- Queue job implementation and testing guidance.
- - Preparing, refining, and enhancing frontend UI prompts before submitting them to Kilo Code AI.

### 2. Kilo Code AI (VS Code Extension)

Used for:

- Implementing the frontend billing interface.
- Developing customer and product interactions.
- Improving the UI based on iterative requirements.
- Making frontend modifications and refinements.
- Assistance with Git and GitHub commands.

## Prompt Documentation

The following prompts are organized in the order they were used during development.

### Phase 1: Project Planning and Backend Design

**01. Project Planning and Requirement Clarification**

Initial discussions about the assignment, project setup, development workflow, database creation, factories, seeders, Laravel Blade, and the appropriate application architecture.

**02. Database Schema Design**

Planning the normalized database schema, identifying the required tables and columns, and understanding the relationships needed for the order and inventory management system.

**03. Frontend Technology and Setup**

Clarifying whether Laravel Blade requires additional installation and planning the frontend implementation approach before generating the UI development prompt.

### Phase 2: Billing Interface Development

**04. Retail Billing Interface – Initial Development Prompt**

Preparing a detailed frontend development prompt covering the retail billing page, customer selection, product management, dynamic table rows, calculations, low-stock alerts, bill preview, and order submission.

**05. Customer-First Workflow and Toast Notifications**

Refining the billing workflow so that product details, low-stock alerts, bill preview, and order generation remain hidden until a customer is successfully selected or created. Also defining the top-right success and error toast notification behaviour.

**06. Successful Order Creation and Form Reset**

Defining the expected behaviour after an order is successfully created, including clearing customer details, resetting product rows, hiding billing sections, clearing calculations, and preparing the interface for the next customer.

### Phase 3: Customer Order History

**07. Customer Order History – Initial Implementation**

Planning the customer order history feature, including customer search, order listing, order details, API integration, and navigation. The initial approach used Bootstrap modals.

**08. Customer Order History – Separate Page Layout**

Refining the initial approach by replacing modal-based order details with dedicated Blade pages. Also addressing customer privacy, route model binding, historical order values, and customer-order ownership validation.

### Phase 4: Frontend Refinements and Validation

**09. Order History UI Improvements**

Making specific adjustments to the order history page, including table columns, customer information display, date formatting, and navigation labels.

**10. Customer Validation and Toast Positioning**

Refining customer name and email validation messages and correcting the toast notification placement to appear as compact notifications in the top-right corner.

**11. Customer Fields and Product Button Adjustments**

Improving the customer name and email input presentation, adding suitable search and placeholder elements, and adjusting the Add Product button width.

**12. Final Billing Interface UI Improvements**

Applying additional UI refinements, including toast duration, button alignment, table input borders, section icons, column naming, and table footer alignment.


**13. Low Stock Alerts – API and UI Fix**

Investigating and fixing the Low Stock Alerts section, which was incorrectly displaying an empty state despite products having low stock. Refining the threshold condition to include products with stock less than or equal to 5, verifying API response handling and frontend rendering, and refreshing the alert list after successful order creation.

## Screenshot Naming Convention

Screenshots are named according to their corresponding prompt numbers to make them easy to identify and review.

For prompts that require multiple screenshots, sequential numbering is used.

Example:

```text
01-project-planning.png
02-database-design.png
03-frontend-setup.png
04-billing-interface-01.png
04-billing-interface-02.png
05-customer-first-workflow.png
```

The screenshots document the enhanced frontend UI prompts submitted to Kilo Code AI. These prompts were initially prepared and refined with ChatGPT before being used for frontend implementation.