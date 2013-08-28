Feature: browsing
    In order to see a lot of ads that I may be like
    As an user
    I need to be able to browse ads

    Scenario: browsing homepage
        Given I have 3 ads
        And I am on the homepage
        Then I should see 3 "div.ad" element
