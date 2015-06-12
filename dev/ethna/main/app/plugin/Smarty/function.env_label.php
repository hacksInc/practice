<?php
/**
 * 環境名の表示用ラベル表示関数
 *
 * @param array $params  $params["env"] = 環境名の識別子("dev" or "stg" or "pro") …省略可
 * @param Smarty
 * @return ラベル("開発環境" or "ステージング環境" or "商用環境")
 */
function smarty_function_env_label($params, &$smarty)
{
    if (is_array($params) && isset($params['env'])) {
        $env = $params['env'];
    } else {
        $env = null;
    }
    
    $label = Util::getEnvLabel($env);
    
    return $label;
}