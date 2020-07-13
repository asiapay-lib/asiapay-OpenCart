# PayDollar/SaimPay/PesoPay Payment plugin for Opencart
Use PayDollar/SaimPay/PesoPays plugin for Opencart to offer ALL payments option.

## Integration
The plugin integrates integrate Opencart with PayDollar/SaimPay/PesoPay payment gateway with All payment method.

## Requirements
This plugin supports Opencart version 3.0.3.2 and higher.

## Installation
1.	Upload the content to the correct folder structure.
    - The Paydollar Payment Module for OpenCart contains \ folder which has to be uploaded to the platforms root directory.
    - Originally, OpenCart has <**root-directory**>/ for the front store and <**root-directory**>/ /**admin** for the admin site. However, if the folder name has changed, you will need to upload the content in \ to the correct folder structure. Eg: <**root-directory**>/<**new-folder-name**>
2.	Check the payment module at the admin site. (**Home -> Extensions -> Payment**)
3.	Install the module and configure the payment setting

## Setup the Datafeed URL on PayDollar/PesoPay/SiamPay
 1. Login to your PayDollar/PesoPay/SiamPay account.
 2. After login, Go to left sidebar on Profile > Profile Settings > Payment Options.
 3. Click the “Enable” radio and set the datafeed URL on “Return Value Link” and click the “Update” button. The datafeed URL should be like this: http://www.yourshop.com/index.php?route=extension/payment/paydollar/callback
 4. On the confirmation page, review your changes then click the “Confirm button”.

 ## Documentation
[Opencart documentation]()

## Support
If you have a feature request, or spotted a bug or a technical problem, create a GitHub issue. For other questions, contact our [Customer Service](https://www.paydollar.com/en/contactus.html).

## License
