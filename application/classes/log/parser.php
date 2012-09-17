<?php
class Log_Parser
{
    public static function parse_file($file)
    {
        $items = file($file);
        $parsed = array();
        foreach ($items as $item) {
            if ($item[0] == '<' OR trim($item) == '')
                continue;
            $parseditem = self::parse($item);
            if (count($parseditem) == 0)
                continue;
            $parsed[] = $parseditem;
        }
        return $parsed;
    }

    public static function parse($item)
    {
        // 2010-10-08 21:01:45 --- ERROR: ErrorException [ 4 ]: syntax error, unexpected '}' ~ APPPATH/classes/controller/worker.php [ 104 ]

        preg_match('/^(?<time>.+?) --- (?<type>.+?): (?<message>.+)$/', $item, $matches);

        // preg_match('/^(?<time>[^\[]+)\[(?<type>.*)\] script: "(?<script>.*)" message: "(?<message>.*)" client: (?<client>[^ ]+) uri: (?<uri>[^ ]+) referer: (?<referer>[^ ]*) agent: "(?<agent>.*)" cookie: "(?<cookie>.*)"/', $item, $matches);
        return $matches;
    }
}
