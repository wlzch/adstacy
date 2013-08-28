Feature: Authentication
    In order to enjoy the full feature of adstacy
    As an user
    I need to be able to register and login

    Scenario: Successfull registration
        Given I am on "/register"
        When I fill in "fos_user_registration_form_email" with "admin@adstacy.com"
        And I fill in "fos_user_registration_form_username" with "adstacy"
        And I fill in "fos_user_registration_form_realName" with "Adstacy Admin"
        And I fill in "fos_user_registration_form_plainPassword_first" with "abcde"
        And I fill in "fos_user_registration_form_plainPassword_second" with "abcde"
        And I press "fos_user_registration_form_register"
        Then the url should match "/settings"

    Scenario: Failed registration because of wrong username
        Given I am on "/register"
        When I fill in "fos_user_registration_form_email" with "admin@adstacy.com"
        And I fill in "fos_user_registration_form_username" with "adstacy admin"
        And I fill in "fos_user_registration_form_realName" with "Adstacy Admin"
        And I fill in "fos_user_registration_form_plainPassword_first" with "abcde"
        And I fill in "fos_user_registration_form_plainPassword_second" with "abcde"
        And I press "fos_user_registration_form_register"
        Then the url should match "/register"

    Scenario: Successfull login
        Given I have users
        And I am on "/"
        When I fill in "_username" with "welly"
        And I fill in "_password" with "welly"
        And I press "_submit"
        Then I should be on homepage

    Scenario: Failed login
        Given I have users
        And I am on "/"
        When I fill in "_username" with "welly"
        And I fill in "_password" with "wellyyy"
        And I press "_submit"
        Then the url should match "/login"
