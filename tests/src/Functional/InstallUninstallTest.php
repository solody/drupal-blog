<?php

namespace Drupal\Tests\blog\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Test for blog module install and uninstall.
 *
 * @group blog
 */
class InstallUninstallTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * The module installer service.
   *
   * @var \Drupal\Core\Extension\ModuleInstallerInterface
   */
  private $moduleInstaller;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->moduleInstaller = \Drupal::service('module_installer');
  }

  /**
   * Tests module re-install.
   */
  public function testReinstall() {
    $this->moduleInstaller->install(['blog']);
    $problems = $this->moduleInstaller->validateUninstall(['blog']);
    $this::assertEmpty($problems);
    $this->moduleInstaller->uninstall(['blog']);
    // Blog module reinstall.
    $this->moduleInstaller->install(['blog']);
    $problems = $this->moduleInstaller->validateUninstall(['blog']);
    $this::assertEmpty($problems);
    $this->moduleInstaller->uninstall(['blog']);
  }
}
