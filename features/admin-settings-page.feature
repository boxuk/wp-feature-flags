Feature: Login as an administrator
    As a maintainer of the site
    I want to be able to configure the plugin
    So that I can configure it for my needs

    Background:
        Given the "wp-plugin-skeleton/wp-plugin-skeleton.php" plugin is active
        And I am logged in as an administrator
        When I go to the dashboard

    Scenario: Confirm settings page is present
        When I go to the "BoxUK" menu
        Then I should see "Enable plugin"
