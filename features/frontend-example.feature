Feature: Visibility of the homepage
  In order to have confidence that my site is accessible
  As a site administrator
  I want to verify I can visit the homepage

  Scenario: Verify the homepage
    Given I am on the homepage
    Then the response status code should be 200
