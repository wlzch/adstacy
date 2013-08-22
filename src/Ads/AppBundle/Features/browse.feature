Feature: browsing
    In order to see a lot of ads that I may be like
    As an user
    I need to be able to browse ads

    Scenario: browsing homepage
        Given I am on the homepage
        And I have 3 posts
        Then I should see 3 "div.post" element
