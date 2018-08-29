<?php

declare(strict_types = 1);
namespace Xutengx\Tool\Traits;

use Xutengx\Tool\Exception\{DecodeXMLException, EncodeXMLException};

trait Character {

	/**
	 * 人性化相对时间
	 * @param int $sTime 目标时间戳
	 * @param string $format 时间格式
	 * @return string
	 */
	public static function friendlyDate(int $sTime = 0, string $format = 'Y-m-d H:i'): string {
		$dTime = time() - $sTime;
		$state = $dTime > 0 ? '前' : '后';
		$dTime = abs($dTime);
		if ($dTime < 60) {
			return $dTime . ' 秒' . $state;
		}
		elseif ($dTime < 3600) {
			return intval($dTime / 60) . ' 分钟' . $state;
		}
		elseif ($dTime < 3600 * 24) {
			return intval($dTime / 3600) . ' 小时' . $state;
		}
		elseif ($dTime < 3600 * 24 * 7) {
			return intval($dTime / (3600 * 24)) . ' 天' . $state;
		}
		elseif ($dTime < 3600 * 24 * 30) {
			return intval($dTime / (3600 * 24 * 7)) . ' 周' . $state;
		}
		elseif ($dTime < 3600 * 24 * 30 * 12) {
			return intval($dTime / (3600 * 24 * 30)) . ' 月' . $state;
		}
		else
			return date($format, $sTime);
	}

	/**
	 * 字符串长度控制(截取)
	 * @param string $string 原字符串
	 * @param int $length 目标长度
	 * @param string $dot 多余展示符
	 * @param string $char eg : utf8/gbk
	 * @return string
	 */
	public static function cutStr(string $string, int $length = 9, string $dot = '...', string $char = 'utf8'): string {
		return (mb_strlen($string) > $length) ? mb_substr($string, 0, $length, $char) . $dot : $string;
	}

	/**
	 * 解析XML格式的字符串
	 * @param string $str
	 * @return array
	 * @throws DecodeXMLException
	 */
	public static function xmlDecode(string $str): array {
		$data       = null;
		$xml_parser = xml_parser_create();
		if (xml_parse($xml_parser, $str, true))
			$data = (json_decode(json_encode(simplexml_load_string($str)), true));
		xml_parser_free($xml_parser);
		if (is_null($data))
			throw new DecodeXMLException;
		return $data;
	}

	/**
	 * XML编码
	 * @param array|string $data 数据
	 * @param string $encoding 数据编码
	 * @param string $root 根节点名
	 * @param string $item 数字索引的子节点名
	 * @param string $attr 根节点属性
	 * @param string $id 数字索引子节点key转换的属性名
	 * @return string
	 * @throws EncodeXMLException
	 */
	public static function xmlEncode($data, string $encoding = 'utf-8', string $root = 'root', string $item = 'item',
		string $attr = '', string $id = 'id'): string {
		$attr = empty($attr) ? '' : ' ' . trim($attr);
		$xml  = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
		$xml  .= "<{$root}{$attr}>";
		$xml  .= static::recursiveData2XML($data, $item, $id);
		$xml  .= "</{$root}>";
		return $xml;
	}

	/**
	 * 数据XML编码
	 * @param array|string $data 数据
	 * @param string $item 数字索引时的节点名称
	 * @param string $id 数字索引key转换为的属性名
	 * @return string
	 * @throws EncodeXMLException
	 */
	public static function recursiveData2XML($data, string $item = 'item', string $id = 'id'): string {
		$xml = $attr = '';
		if (is_array($data)) {
			foreach ($data as $key => $val) {
				if (is_numeric($key)) {
					$id && $attr = " {$id}=\"{$key}\"";
					$key = $item;
				}
				$xml .= "<{$key}{$attr}>";
				$xml .= (is_array($val) || is_object($val)) ? static::recursiveData2XML($val, $item, $id) : $val;
				$xml .= "</{$key}>";
			}
		}
		elseif (is_string($data))
			return $data;
		else throw new EncodeXMLException;
		return $xml;
	}

}
