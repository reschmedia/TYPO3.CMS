<?php
namespace TYPO3\CMS\Fluid\Tests\Unit\ViewHelpers\Security;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\UserAspect;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Security\IfAuthenticatedViewHelper;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Testcase for security.ifAuthenticated view helper
 */
class IfAuthenticatedViewHelperTest extends ViewHelperBaseTestcase
{
    /**
     * @var \TYPO3\CMS\Fluid\ViewHelpers\Security\IfAuthenticatedViewHelper
     */
    protected $viewHelper;

    /**
     * @var Context
     */
    protected $context;

    protected function setUp()
    {
        parent::setUp();
        $this->context = GeneralUtility::makeInstance(Context::class);
        $this->viewHelper = new IfAuthenticatedViewHelper();
        $this->injectDependenciesIntoViewHelper($this->viewHelper);
        $this->viewHelper->initializeArguments();
    }

    protected function tearDown()
    {
        GeneralUtility::removeSingletonInstance(Context::class, $this->context);
    }

    /**
     * @test
     */
    public function viewHelperRendersThenChildIfFeUserIsLoggedIn()
    {
        $user = new FrontendUserAuthentication();
        $user->user['uid'] = 13;
        $this->context->setAspect('frontend.user', new UserAspect($user));

        $actualResult = $this->viewHelper->renderStatic(
            ['then' => 'then child', 'else' => 'else child'],
            function () {
            },
            $this->prophesize(RenderingContextInterface::class)->reveal()
        );

        $this->assertEquals('then child', $actualResult);
    }

    /**
     * @test
     */
    public function viewHelperRendersElseChildIfFeUserIsNotLoggedIn()
    {
        $this->context->setAspect('frontend.user', new UserAspect());

        $actualResult = $this->viewHelper->renderStatic(
            ['then' => 'then child', 'else' => 'else child'],
            function () {
            },
            $this->prophesize(RenderingContextInterface::class)->reveal()
        );

        $this->assertEquals('else child', $actualResult);
    }
}
