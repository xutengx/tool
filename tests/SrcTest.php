<?php
declare(strict_types = 1);

use PHPUnit\Framework\TestCase;
use Xutengx\Tool\Tool;

final class SrcTest extends TestCase {

	protected $dir;

	public function setUp() {
		$this->dir = dirname(__DIR__) . '/storage/forTest/';
	}

	public function testCheckIp() {
		$this->assertInstanceOf(Tool::class, $tool = new Tool);
		$this->assertFalse($tool->checkIp('0.0.0.0'));
		$this->assertTrue($tool->checkIp('192.168.188.128'));
		$this->assertTrue($tool->checkIp('127.0.0.0'));
		$this->assertTrue($tool->checkIp('127.0.0.1'));
		$this->assertTrue($tool->checkIp('127.255.255.255'));
		$this->assertTrue($tool->checkIp('192.168.0.0'));
		$this->assertTrue($tool->checkIp('192.168.255.255'));
		$this->assertTrue($tool->checkIp('172.16.0.0'));
		$this->assertTrue($tool->checkIp('172.31.255.255'));
		$this->assertTrue($tool->checkIp('10.0.0.0'));
		$this->assertTrue($tool->checkIp('10.255.255.255'));
		try {
			$tool->checkIp('256.333.0.0');
		} catch (InvalidArgumentException $e) {
			$this->assertEquals('Invalid IP[256.333.0.0].', $e->getMessage());
		}
		$this->assertFalse($tool->checkIp('10.255.255.255', [
			['192.168.1.2', '192.168.1.3']
		]));
	}

	public function testUuid() {
		$this->assertInstanceOf(Tool::class, $tool = new Tool);
		$this->assertEquals(36, strlen($tool->uuid()));

		$count = 50000;
		$ids   = [];
		for ($i = 0; $i < $count; $i++) {
			$ids[] = $tool::uuid();
		}
		$arr = array_unique($ids);
		$this->assertEquals($count, count($arr), '是否存在重复');
	}

	public function testFriendlyDate() {
		$this->assertInstanceOf(Tool::class, $tool = new Tool);

		$this->assertEquals('36 秒前', $tool->friendlyDate(time() - 36));
		$this->assertEquals('36 秒后', $tool->friendlyDate(time() + 36));
		$this->assertEquals('6 分钟前', $tool->friendlyDate(time() - 360));
		$this->assertEquals('6 分钟后', $tool->friendlyDate(time() + 360));
		$this->assertEquals('1 小时前', $tool->friendlyDate(time() - 3600));
		$this->assertEquals('1 小时后', $tool->friendlyDate(time() + 3600));
		$this->assertEquals('3 小时前', $tool->friendlyDate(time() - 3600 * 3));
		$this->assertEquals('3 小时后', $tool->friendlyDate(time() + 3600 * 3));
		$this->assertEquals('19 小时前', $tool->friendlyDate(time() - 3600 * 19));
		$this->assertEquals('19 小时后', $tool->friendlyDate(time() + 3600 * 19));
		$this->assertEquals('1 天前', $tool->friendlyDate(time() - 3600 * 25));
		$this->assertEquals('1 天后', $tool->friendlyDate(time() + 3600 * 25));
		$this->assertEquals('3 天前', $tool->friendlyDate(time() - 3600 * 24 * 3));
		$this->assertEquals('3 天后', $tool->friendlyDate(time() + 3600 * 24 * 3));
		$this->assertEquals('1 周前', $tool->friendlyDate(time() - 3600 * 24 * 7));
		$this->assertEquals('1 周后', $tool->friendlyDate(time() + 3600 * 24 * 7));
		$this->assertEquals('2 周前', $tool->friendlyDate(time() - 3600 * 24 * 15));
		$this->assertEquals('2 周后', $tool->friendlyDate(time() + 3600 * 24 * 15));
		$this->assertEquals('3 周前', $tool->friendlyDate(time() - 3600 * 24 * 22));
		$this->assertEquals('3 周后', $tool->friendlyDate(time() + 3600 * 24 * 22));
		$this->assertEquals('1 月前', $tool->friendlyDate(time() - 3600 * 24 * 31));
		$this->assertEquals('1 月后', $tool->friendlyDate(time() + 3600 * 24 * 31));
		$this->assertEquals('2 月前', $tool->friendlyDate(time() - 3600 * 24 * 61));
		$this->assertEquals('2 月后', $tool->friendlyDate(time() + 3600 * 24 * 61));
	}

	public function testCutStr() {
		$this->assertInstanceOf(Tool::class, $tool = new Tool);

		$this->assertEquals('abc...', $tool->cutStr('abcdefgh', 3));
		$this->assertEquals('abcde...', $tool->cutStr('abcdefgh', 5));
		$this->assertEquals('天天想你天...', $tool->cutStr('天天想你天天问自己', 5));
		$this->assertEquals('天天想你', $tool->cutStr('天天想你', 5));
		$this->assertEquals('天天xxx', $tool->cutStr('天天想你天天问自己', 2, 'xxx'));
	}

	public function testXmlDecodeEncode() {
		$this->assertInstanceOf(Tool::class, $tool = new Tool);
		$array     = [
			'family_zhang' => [
				'father' => 'zhangxin',
				'mother' => 'liuye',
				'son'    => 'zhangxixi',
			],
			'family_wang'  => [
				'father' => 'wangxin',
				'mother' => 'wenyehua',
				'son'    => 'wangxixi',
			]
		];
		$array2xml = '<?xml version="1.0" encoding="utf-8"?><root><family_zhang><father>zhangxin</father><mother>liuye</mother><son>zhangxixi</son></family_zhang><family_wang><father>wangxin</father><mother>wenyehua</mother><son>wangxixi</son></family_wang></root>';

		$this->assertEquals($array2xml, $tool->xmlEncode($array));
		$this->assertEquals($array, $tool->xmlDecode($tool->xmlEncode($array)));
	}

	public function testGenerateFilename() {
		$this->assertInstanceOf(Tool::class, $tool = new Tool);

		$count = 50000;
		$ids   = [];
		for ($i = 0; $i < $count; $i++) {
			$ids[] = $tool::generateFilename('/tmp/', '.xml');
		}
		$arr = array_unique($ids);
		$this->assertEquals($count, count($arr), '是否存在重复');
	}

	public function testFilePutContents() {
		$this->assertInstanceOf(Tool::class, $tool = new Tool);
		$dir     = $this->dir . '/test/rtt/asd/';
		$textOld = 'test123123';
		$text    = 'test';

		$this->assertDirectoryNotExists(dirname(dirname($dir)));

		$fileName  = $tool::generateFilename($dir, 'txt');
		$fileName2 = $tool::generateFilename($dir, 'txt');
		$tool::filePutContents($fileName, $textOld);
		$tool::filePutContents($fileName2, $textOld);
		$tool::filePutContents($fileName, $text);
		$tool::filePutContents($fileName2, $text);
		$this->assertFileExists($fileName);
		$this->assertFileExists($fileName2);
		$this->assertEquals($text, file_get_contents($fileName));
		$this->assertEquals($text, file_get_contents($fileName2));
	}

	public function testGetFiles() {
		$this->assertInstanceOf(Tool::class, $tool = new Tool);

		$this->assertEquals(2, count($tool::getFiles($this->dir)));
	}


	public function testRecursiveMakeDirectory(){
		$this->assertInstanceOf(Tool::class, $tool = new Tool);

		$dir = $this->dir.'/forTest/ddd/aa/rrr';
		$this->assertTrue($tool::recursiveMakeDirectory($dir), '递归创建不存在的目录');
		$this->assertDirectoryExists($dir);
		$this->assertTrue($tool::recursiveMakeDirectory($dir), '递归创建已存在的目录');
		$this->assertDirectoryExists($dir);

	}

	public function testRecursiveDeleteDirectory() {
		$this->assertInstanceOf(Tool::class, $tool = new Tool);

		$tool::recursiveDeleteDirectory($this->dir);
		$this->assertEquals(0, count($tool::getFiles($this->dir)));
		$this->assertDirectoryExists($this->dir, '递归删除目录(绝对路径)下的所有文件,不包括自身');

	}

}


