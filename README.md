# IBAN Nummber field for Wordpress Elementor Forms

An extension of a standard form TextField that certifies the input of a valid Dutch bank account number.
Bank accounts part of SEPA are also called IBAN numbers. 
More about IBAN number format in PHP, see GIT Repo: https://github.com/globalcitizen/php-iban.git

The Field has a placeholder for a readable IBAN nummber with spaces
The Field has a regexp pattern to match the format before submit
The Field validator calls library of the above GIT Repo. 

The top level function $verify_iban has been modified slightly, to ensure the number is from a valid Dutch bank.
Actually this check also happens in the function $validate by enforcing the regexp pattern.

