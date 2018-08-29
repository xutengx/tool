<?php

declare(strict_types = 1);
namespace Xutengx\Tool\Traits;

use Exception;

/**
 * 文件操作
 */
trait File {

	/**
	 * 递归删除目录(绝对路径)下的所有文件,不包括自身
	 * @param string $dirName
	 * @return void
	 */
	public static function recursiveDeleteDirectory(string $dirName): void {
		if (is_dir($dirName) && $dirArray = scandir($dirName)) {
			foreach ($dirArray as $k => $v) {
				if ($v !== '.' && $v !== '..') {
					if (is_dir($dirName . '/' . $v)) {
						static::recursiveDeleteDirectory($dirName . '/' . $v);
						rmdir($dirName . '/' . $v);
					}
					else
						unlink($dirName . '/' . $v);
				}
			}
		}
	}

	/**
	 * 写入文件
	 * @param string $filename 文件名(绝对路径)
	 * @param string $text
	 * @param int $lockType LOCK_EX LOCK_NB
	 * @return bool
	 */
	public static function filePutContents(string $filename, string $text, int $lockType = LOCK_EX): bool {
		if (!is_file($filename))
			if (is_dir(dirname($filename)) || static::recursiveMakeDirectory(dirname($filename)))
				touch($filename);
		return file_put_contents($filename, $text, $lockType) === false ? false : true;
	}

	/**
	 * 返回文件夹下的所有文件 组成的一维数组
	 * @param string $dirName 文件夹路径(绝对)
	 * @return array 一维数组
	 * @throws Exception
	 */
	public static function getFiles(string $dirName = ''): array {
		$dirName = rtrim($dirName, '/');
		$arr     = [];
		if (is_dir($dirName) && $dir_arr = scandir($dirName)) {
			foreach ($dir_arr as $k => $v)
				if ($v !== '.' && $v !== '..')
					if (is_dir($dirName . '/' . $v))
						$arr = array_merge($arr, static::getFiles($dirName . '/' . $v));
					else
						$arr[] = $dirName . '/' . $v;
			return $arr;
		}
		else
			throw new Exception("$dirName is not readable path!");
	}

	/**
	 * 生成随机文件名
	 * @param string $dir 文件所在的目录(绝对)
	 * @param string $ext 文件后缀
	 * @param string $uni 唯一标识
	 * @return string
	 */
	public static function generateFilename(string $dir, string $ext, string $uni = ''): string {
		return rtrim($dir, '/') . '/' . md5(uniqid($uni, true)) . '.' . trim($ext, '.');
	}

	/**
	 * 递归生成目录(绝对路径)
	 * @param string $dir
	 * @param int $mode
	 * @return bool
	 */
	public static function recursiveMakeDirectory(string $dir, int $mode = 0777): bool {
		return (is_dir(dirname($dir)) || static::recursiveMakeDirectory(dirname($dir))) ? mkdir($dir, $mode) : true;
	}

}
