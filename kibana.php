<?php

$schema = '{
  "mappings": {
    "statistics_details": {
      "properties": {
        "entry_id": {
          "type": "string",
          "index": "not_analyzed"
        },
        "provider": {
          "type": "string",
          "index": "not_analyzed"
        },
        "variable": {
          "type": "string",
          "index": "not_analyzed"
        },
        "value_string": {
          "type": "string",
          "index": "not_analyzed"
        },
        "value_int": {
          "type": "integer"
        },
        "timestamp": {
          "type": "date",
          "format": "epoch_second"
        }
      }
    },
    "statistics_main": {
      "properties": {
        "install_id": {
          "type": "string",
          "index": "not_analyzed"
        },
        "entry_id": {
          "type": "string",
          "index": "not_analyzed"
        },
        "forwarded": {
          "type": "boolean"
        },
        "PHP_version": {
          "type": "string",
          "index": "not_analyzed"
        },
        "PHP_version_full": {
          "type": "string",
          "index": "not_analyzed"
        },
        "PHP_sapi": {
          "type": "string",
          "index": "not_analyzed"
        },
        "PHP_int_size": {
          "type": "integer"
        },
        "PHP_safe_mode": {
          "type": "boolean"
        },
        "PHP_open_basedir": {
          "type": "boolean"
        },
        "PHP_memory_limit": {
          "type": "string",
          "index": "not_analyzed"
        },
        "PHP_allow_url_fopen": {
          "type": "boolean"
        },
        "PHP_allow_url_include": {
          "type": "boolean"
        },
        "PHP_file_uploads": {
          "type": "boolean"
        },
        "PHP_upload_max_filesize": {
          "type": "string",
          "index": "not_analyzed"
        },
        "PHP_post_max_size": {
          "type": "string",
          "index": "not_analyzed"
        },
        "PHP_disable_functions": {
          "type": "string",
          "index": "not_analyzed"
        },
        "PHP_disable_classes": {
          "type": "string",
          "index": "not_analyzed"
        },
        "PHP_enable_dl": {
          "type": "boolean"
        },
        "PHP_magic_quotes_gpc": {
          "type": "boolean"
        },
        "PHP_register_globals": {
          "type": "boolean"
        },
        "PHP_filter": {
          "type": "string",
          "index": "not_analyzed"
        },
        "PHP_zend_ze1_compatibility_mode": {
          "type": "boolean"
        },
        "PHP_unicode_semantics": {
          "type": "boolean"
        },
        "PHP_zend_thread_safty": {
          "type": "boolean"
        },
        "System_os": {
          "type": "string",
          "index": "not_analyzed"
        },
        "System_httpd": {
          "type": "string",
          "index": "not_analyzed"
        },
        "System_ip": {
          "type": "string",
          "index": "not_analyzed"
        },
        "phpBB_config_version": {
          "type": "string",
          "index": "not_analyzed"
        },
        "phpBB_config_version_major": {
          "type": "string",
          "index": "not_analyzed"
        },
        "phpBB_dbms": {
          "type": "string",
          "index": "not_analyzed"
        },
        "phpBB_config_num_posts": {
          "type": "integer"
        },
        "phpBB_config_num_topics": {
          "type": "integer"
        },
        "phpBB_config_num_users": {
          "type": "integer"
        },
        "timestamp": {
          "type": "date",
          "format": "epoch_second"
        }
      }
    }
  }
}
';

function toByteSize($p_sFormatted) {
    $aUnits = array('B'=>0, 'KB'=>1, 'MB'=>2, 'GB'=>3, 'TB'=>4, 'PB'=>5, 'EB'=>6, 'ZB'=>7, 'YB'=>8);
    $sUnit = strtoupper(trim(substr($p_sFormatted, -2)));
    if (intval($sUnit) !== 0) {
        $sUnit = 'B';
    }
    if (!in_array($sUnit, array_keys($aUnits))) {
        return false;
    }
    $iUnits = trim(substr($p_sFormatted, 0, strlen($p_sFormatted) - 2));
    if (!intval($iUnits) == $iUnits) {
        return false;
    }
    return $iUnits * pow(1024, $aUnits[$sUnit]);
}

function request($method, $endpoint, $data = null) {
    $ci = curl_init();
    curl_setopt($ci, CURLOPT_URL, 'http://TOCHANGE_ELASTICSERACH_IP:9200/'.$endpoint);
    curl_setopt($ci, CURLOPT_PORT, 9200);
    curl_setopt($ci, CURLOPT_TIMEOUT, 200);
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ci, CURLOPT_FORBID_REUSE, 0);

    if (!empty($data)) {
        curl_setopt($ci, CURLOPT_POSTFIELDS, $data);
    }

    curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $method);

    $result =  curl_exec($ci);
    $httpcode = curl_getinfo($ci, CURLINFO_HTTP_CODE);
    if ($httpcode >= 400) {
        echo $result;
    }
}

function push($data) {
    request('PUT', '_bulk', $data);
}

$start = microtime(true);

request('DELETE', 'phpbb_questionnaire');
printf("[%s] Index deleted\n", gmdate('H:i:s', microtime(true) - $start));
request('PUT', 'phpbb_questionnaire', $schema);
printf("[%s] Index created\n", gmdate('H:i:s', microtime(true) - $start));

$query = 'SELECT `install_id`,
`entry_id`,
`forwarded`,
`PHP_version`,
`PHP_sapi`,
`PHP_int_size`,
`PHP_safe_mode`,
`PHP_open_basedir`,
`PHP_memory_limit`,
`PHP_allow_url_fopen`,
`PHP_allow_url_include`,
`PHP_file_uploads`,
`PHP_upload_max_filesize`,
`PHP_post_max_size`,
`PHP_disable_functions`,
`PHP_disable_classes`,
`PHP_enable_dl`,
`PHP_magic_quotes_gpc`,
`PHP_register_globals`,
`PHP_filter.default` as PHP_filter,
`PHP_zend.ze1_compatibility_mode` as PHP_zend_ze1_compatibility_mode,
`PHP_unicode.semantics` as PHP_unicode_semantics,
`PHP_zend_thread_safty`,
`System_os`,
`System_httpd`,
`System_ip`,
`phpBB_config_version`,
`phpBB_dbms`,
`phpBB_config_num_posts`,
`phpBB_config_num_topics`,
`phpBB_config_num_users`,
`timestamp` FROM statistics_main';

$detailsQuery = 'SELECT d.*, m.timestamp FROM statistics_details d INNER JOIN statistics_main m ON m.entry_id = d.entry_id';

$mysqli = new mysqli('SERVER', 'USER', 'PASS', 'DB');
$st = $mysqli->prepare($query);

$res = $mysqli->query($query, MYSQLI_USE_RESULT);

printf("[%s] Pushing main\n", gmdate('H:i:s', microtime(true) - $start));

$i = 0;
$j = 0;
$data = '';
while ($row = $res->fetch_assoc()) {
    $i++;
    $j++;

    $row['PHP_disable_functions'] = explode(',', $row['PHP_disable_functions']);
    $row['PHP_disable_classes'] = explode(',', $row['PHP_disable_classes']);
    $row['PHP_upload_max_filesize'] = toByteSize($row['PHP_upload_max_filesize']);
    $row['PHP_memory_limit'] = toByteSize($row['PHP_memory_limit']);
    $row['forwarded'] = (bool) $row['forwarded'];
    $row['PHP_safe_mode'] = (bool) $row['PHP_safe_mode'];
    $row['PHP_open_basedir'] = (bool) $row['PHP_open_basedir'];
    $row['PHP_allow_url_fopen'] = (bool) $row['PHP_allow_url_fopen'];
    $row['PHP_allow_url_include'] = (bool) $row['PHP_allow_url_include'];
    $row['PHP_file_uploads'] = (bool) $row['PHP_file_uploads'];
    $row['PHP_enable_dl'] = (bool) $row['PHP_enable_dl'];
    $row['PHP_magic_quotes_gpc'] = (bool) $row['PHP_magic_quotes_gpc'];
    $row['PHP_register_globals'] = (bool) $row['PHP_register_globals'];
    $row['PHP_zend_ze1_compatibility_mode'] = (bool) $row['PHP_zend_ze1_compatibility_mode'];
    $row['PHP_unicode_semantics'] = (bool) $row['PHP_unicode_semantics'];
    $row['PHP_zend_thread_safty'] = (bool) $row['PHP_zend_thread_safty'];
    $row['PHP_int_size'] = (int) $row['PHP_int_size'];
    $row['phpBB_config_num_posts'] = (int) $row['phpBB_config_num_posts'];
    $row['phpBB_config_num_topics'] = (int) $row['phpBB_config_num_topics'];
    $row['phpBB_config_num_users'] = (int) $row['phpBB_config_num_users'];
    $row['timestamp'] = strtotime($row['timestamp']);
    $row['PHP_version_full'] = $row['PHP_version'];
    $phpVersion = explode('.', $row['PHP_version']);
    $row['PHP_version'] = $phpVersion[0].'.'.$phpVersion[1];
    $phpBBVersion = explode('.', $row['phpBB_config_version']);
    $row['phpBB_config_version_major'] = $phpBBVersion[0].'.'.$phpBBVersion[1];

    $data .= json_encode(['create' => ['_index' => 'phpbb_questionnaire', '_type' => 'statistics_main', '_id' => $row['entry_id']]])."\n";
    $data .= json_encode($row)."\n";

    if ($i >= 50000) {
        printf("[%s] %d entries pushed to easticsearch\n", $j, gmdate('H:i:s', microtime(true) - $start));
        push($data);
        $i = 0;
        $data = '';
    }
}
push($data);
printf("[%s] %d entries pushed to easticsearch\n", $j, gmdate('H:i:s', microtime(true) - $start));

printf("[%s] Pushing details\n", gmdate('H:i:s', microtime(true) - $start));

$res = $mysqli->query($detailsQuery, MYSQLI_USE_RESULT);

$i = 0;
$j = 0;
$data = '';
while ($row = $res->fetch_assoc()) {
    $i++;
    $j++;

    $row['value_int'] = (int) $row['value_int'];
    $row['timestamp'] = strtotime($row['timestamp']);

    $data .= json_encode(['create' => ['_index' => 'phpbb_questionnaire', '_type' => 'statistics_details', '_id' => $row['entry_id'].$row['variable']]])."\n";
    $data .= json_encode($row)."\n";

    if ($i >= 50000) {
        printf("[%s] %d entries pushed to easticsearch\n", $j, gmdate('H:i:s', microtime(true) - $start));
        push($data);
        $i = 0;
        $data = '';
    }
}
push($data);
printf("[%s] %d entries pushed to easticsearch\n", $j, gmdate('H:i:s', microtime(true) - $start));
