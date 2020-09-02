<?php

use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use extas\components\plugins\PluginRepository;
use extas\components\plugins\Plugin;
use extas\components\plugins\repositories\PluginFieldUuid;
use extas\components\extensions\ExtensionRepository;
use extas\components\extensions\Extension;
use Ramsey\Uuid\Uuid;

/**
 * Class PluginUuidFieldTest
 *
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginFieldUuidTest extends TestCase
{
    /**
     * @var PluginRepository|null
     */
    protected ?PluginRepository $pluginRepo = null;

    /**
     * @var ExtensionRepository|null
     */
    protected ?ExtensionRepository $extRepo = null;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
        $this->pluginRepo = new PluginRepository;
        $this->extRepo = new ExtensionRepository();
    }

    public function tearDown(): void
    {
        $this->pluginRepo->delete([Plugin::FIELD__CLASS => PluginFieldUuid::class]);
        $this->extRepo->delete([Extension::FIELD__CLASS => 'NotExistingClass']);
    }

    public function testUuid4()
    {
        $this->installUuidPlugin();

        /**
         * @var $extension Extension
         */
        $extension = $this->extRepo->create(new Extension([
            Extension::FIELD__CLASS => 'NotExistingClass',
            Extension::FIELD__SUBJECT => '@uuid4'
        ]));

        $this->assertEquals(false, '@uuid4' == $extension->getSubject());
    }

    public function testUuid5()
    {
        $this->installUuidPlugin();

        /**
         * @var $extension Extension
         */
        $extension = $this->extRepo->create(new Extension([
            Extension::FIELD__CLASS => 'NotExistingClass',
            Extension::FIELD__SUBJECT => '@uuid5.'.Uuid::NAMESPACE_URL.'.test'
        ]));

        $this->assertEquals(false, '@uuid3.'.Uuid::NAMESPACE_URL.'.test' == $extension->getSubject());
    }

    public function testUuid6()
    {
        $this->installUuidPlugin();

        /**
         * @var $extension Extension
         */
        $extension = $this->extRepo->create(new Extension([
            Extension::FIELD__CLASS => 'NotExistingClass',
            Extension::FIELD__SUBJECT => '@uuid6'
        ]));

        $this->assertEquals(false, '@uuid6' == $extension->getSubject());
    }

    public function testAllAsArray()
    {
        $this->installUuidPlugin();

        $sourceMethods = [
            '@uuid4',
            '@uuid5.'.Uuid::NAMESPACE_URL.'.test',
            '@uuid6'
        ];

        /**
         * @var $extension Extension
         */
        $extension = $this->extRepo->create(new Extension([
            Extension::FIELD__CLASS => 'NotExistingClass',
            Extension::FIELD__SUBJECT => 'test',
            Extension::FIELD__METHODS => $sourceMethods
        ]));

        $methods = $extension->getMethods();
        foreach ($methods as $method) {
            $this->assertFalse(in_array($method, $sourceMethods));
        }
    }

    public function testDirectAccess()
    {
        $plugin = new PluginFieldUuid();
        $sourceMethods = [
            '@uuid4',
            '@uuid5.'.Uuid::NAMESPACE_URL.'.test',
            '@uuid6'
        ];

        /**
         * @var $extension Extension
         */
        $extension = new Extension([
            Extension::FIELD__CLASS => 'NotExistingClass',
            Extension::FIELD__SUBJECT => '@uuid4',
            Extension::FIELD__METHODS => $sourceMethods
        ]);

        $plugin($extension);

        $this->assertFalse('@uuid4' == $extension->getSubject());
        $methods = $extension->getMethods();
        foreach ($methods as $method) {
            $this->assertFalse(in_array($method, $sourceMethods));
        }
    }

    protected function installUuidPlugin()
    {
        $this->pluginRepo->create(new Plugin([
            Plugin::FIELD__CLASS => PluginFieldUuid::class,
            Plugin::FIELD__STAGE => 'extas.extensions.create.before'
        ]));
    }
}
