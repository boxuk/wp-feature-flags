<?php
/**
 * Context class for our behat features.
 *
 * @package BoxUk\WpPluginSkeleton
 */

declare ( strict_types=1 );

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\ResponseTextException;
use PaulGibbs\WordpressBehatExtension\Context\RawWordpressContext;
use PaulGibbs\WordpressBehatExtension\Context\Traits\UserAwareContextTrait;

/**
 * Define application features from the specific context.
 *
 * phpcs:disable Generic.Commenting.DocComment.MissingShort
 */
class FeatureContext extends RawWordpressContext {
	use UserAwareContextTrait;
	use ArrayCheckingTrait;

	/**
	 * @When I fill in the :formName form with
	 *
	 * @param string    $form Form.
	 * @param TableNode $fields Fields of the form.
	 *
	 * @throws ElementNotFoundException If element is not found.
	 */
	public function iFillInTheFormWith( string $form, TableNode $fields ): void {
		foreach ( $fields->getHash() as $field ) {
			$this->getSession()->getPage()->fillField( 'Name', $field['Name'] );
		}
	}

	/**
	 * @When I submit the form
	 */
	public function iSubmitTheForm(): void {
		$this->getSession()->getPage()->pressButton( 'submit' );
	}

	/**
	 * @Then I should see :item listed
	 *
	 * @param string $item Item to check if listed.
	 *
	 * @throws ResponseTextException If response text does not contain the item.
	 */
	public function iShouldSeeListed( string $item ): void {
		$this->assertSession()->pageTextContains( $item );
	}

	/**
	 * @Given I have enabled the setting :setting
	 *
	 * @param string $setting The setting to enable.
	 */
	public function iHaveEnabledSetting( string $setting ): void {
		update_option( $setting, '1' );
	}
}
