# sticky.io-to-shopify-discount-coupon-generator

## Overview
This project automates the process of generating a $50 coupon in Shopify when a user places their first subscription order on Sticky.io (Limelight CRM). 
The workflow ensures seamless integration between Sticky.io and Shopify, rewarding users with a discount for future purchases.

## Features
- Detects first-time subscription orders from Sticky.io.
- Populates the main database with user details.
- Creates a $50 discount coupon in Shopify via the Shopify Developer API.
- Sends an email notification to the user with the coupon details.
- Ensures the coupon remains valid for 1 year.
- Applies a $50 discount on any product over $200 in Shopify.

## Tech Stack
- **Backend:** PHP / Node.js (Based on your implementation)
- **CRM:** Sticky.io (Limelight CRM)
- **E-commerce Platform:** Shopify
- **API Integration:** Shopify Developer API, Sticky.io Postback URL
- **Database:** MySQL / MongoDB (Mention your DB)
- **Email Service:** (Specify the service used, e.g., SMTP, SendGrid, Mailgun)

## Installation
1. Clone the repository:  
   ```bash
   git clone https://github.com/your-username/stickyio-shopify-coupon-automation.git
