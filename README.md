# sticky.io-to-shopify-discount-coupon-generator

## Overview
This project automates the process of generating a $50 coupon in Shopify when a user places their first subscription order on Sticky.io (formerly Limelight CRM). 
The workflow ensures seamless integration between Sticky.io and Shopify, rewarding users with a discount for future purchases.

## Features
- Detects first-time subscription orders from Sticky.io (formerly Limelight CRM).
- Populates the main database(MySQL) with user details.
- Creates a $50 discount coupon in Shopify via the Shopify Developer API.
- After creation of coupon sends an email notification to the user with the coupon details.
- Ensures the coupon remains valid for 1 year.
- Applies a $50 discount on any product over $200 in Shopify store.

## Tech Stack
- **Backend:** Core PHP
- **CRM:** Sticky.io ( formerly Limelight CRM)
- **E-commerce Platform:** Shopify
- **API Integration:** Shopify Developer API, Sticky.io Postback URL
- **Database:** MySQL 
- **Email Service:** SMTP

## Installation
1. Clone the repository:  
   ```bash
   git clone https://github.com/SubhadipBhattacharjee/sticky.io-to-shopify-discount-coupon-generator.git
