<?php

namespace ZX\Tool;

use Exception;
use ZX\Generator;

class File
{
    //写入文件 $append强制追加内容
    public static function writeToFile(string $fileName, string $filePath, string $content, string $model = 'a', bool $append = false)
    {
        try {
            $path = $filePath . DIRECTORY_SEPARATOR . $fileName;

            if ($append) {
                $file = fopen($path, $model);
                fwrite($file, $content);
                fclose($file);
            } else {
                if (file_exists($path)) {
                    return true;
                } else {
                    $file = fopen($path, $model);
                    fwrite($file, $content);
                    fclose($file);
                }
            }

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

    }

    public static function deldir($path)
    {
        //如果是目录则继续
        if (is_dir($path)) {
            //扫描一个文件夹内的所有文件夹和文件并返回数组
            $p = scandir($path);
            foreach ($p as $val) {
                //排除目录中的.和..
                if ($val != "." && $val != "..") {
                    //如果是目录则递归子目录，继续操作
                    if (is_dir($path . $val)) {
                        //子目录中操作删除文件夹和文件
                        self::deldir($path . $val . '/');
                        //目录清空后删除空文件夹
                        rmdir($path . $val . '/');
                    } else {
                        //如果是文件直接删除
                        unlink($path . $val);
                    }
                }
            }
        }
    }

    public static function makeFile(string $path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    /**
     * @param bool $custom 模板是否自定义 true使用自定义模板，false 使用提供的模板
     * @param string $filePath 模板文件名称
     * @param string $templateName 模板名称
     * @return false|string
     * @throws Exception
     */
    public static function getFileContent(Generator $Generator, string $filePath = '', string $templateName = '')
    {
        if (empty($templateName)) {
            throw new Exception('template name is null');
        }
        $templatePath = $Generator::getTemplatePath() . DIRECTORY_SEPARATOR . $templateName . DIRECTORY_SEPARATOR . $filePath;

        if (!file_exists($templatePath)) {
            throw new Exception('file is not exist');
        }
        $content = file_get_contents($templatePath);

        return $content;
    }

}
