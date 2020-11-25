<?php
declare(strict_types=1);

namespace HyperfTest\Phar;


use Hyperf\Phar\Package;
use PHPUnit\Framework\TestCase;

class PackageTest extends TestCase
{

    /**
     * 测试默认值
     */
    public function testDefaults()
    {
        $package = new Package(array(), 'dirs/');

        $this->assertEquals(array(), $package->getBins());
        $this->assertEquals('dirs/', $package->getDirectory());
        $this->assertEquals(null, $package->getName());
        $this->assertEquals('dirs', $package->getShortName());
        $this->assertEquals('vendor/', $package->getPathVendor());
    }

    /**
     * 测试自定义数据
     */
    public function testPackage()
    {
        $package = new Package(array(
            'name' => 'hyperf/phar',
            'bin' => array('bin/hyperf.php', 'bin/phar.php'),
            'config' => array(
                'vendor-dir' => 'src/vendors'
            )
        ), 'dirs/');
        $this->assertEquals(array('bin/hyperf.php', 'bin/phar.php'), $package->getBins());
        $this->assertEquals("hyperf/phar",$package->getName());
        $this->assertEquals("phar",$package->getShortName());
        $this->assertEquals("src/vendors/",$package->getPathVendor());
    }


    public function testBundleWillContainComposerJsonButNotVendor()
    {
        $dir = realpath(__DIR__ . '/../fixtures/03-project-with-phars') . '/';
        $package = new Package(array(), $dir);
        $bundle = $package->bundle();

        $this->assertTrue($bundle->checkContains($dir . 'composer.json'));
        $this->assertFalse($bundle->checkContains($dir . 'vendor/autoload.php'));
        $this->assertFalse($bundle->checkContains($dir . 'composer.phar'));
        $this->assertTrue($bundle->checkContains($dir . 'phar-composer.phar'));
    }

    public function testBundleWillNotContainComposerPharInRoot()
    {
        $dir = realpath(__DIR__ . '/../fixtures/03-project-with-phars') . '/';
        $package = new Package(array(), $dir);
        $bundle = $package->bundle();

        $this->assertFalse($bundle->checkContains($dir . 'composer.phar'));
        $this->assertTrue($bundle->checkContains($dir . 'phar-composer.phar'));
    }

    public function testBundleWillContainComposerPharFromSrc()
    {
        $dir = realpath(__DIR__ . '/../fixtures/04-project-with-phars-in-src') . '/';
        $package = new Package(array(), $dir);
        $bundle = $package->bundle();

        $this->assertTrue($bundle->checkContains($dir . 'composer.json'));
        $this->assertTrue($bundle->checkContains($dir . 'src/composer.phar'));
        $this->assertTrue($bundle->checkContains($dir . 'src/phar-composer.phar'));
    }
}