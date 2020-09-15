<?php
/**
 * @param string $variable
 *
 * @return string
 */
function getEnvironmentVariable(string $variable) : string
{
    $value = strval(filter_input(INPUT_ENV, $variable));

    if (empty($value)) {
        echo "Environment variable " . $variable . " not set!\n";

        die(1);
    }

    return $value;
}

$AUTO_VERSION_TAG_TOKEN = getEnvironmentVariable("AUTO_VERSION_TAG_TOKEN");
$GITLAB_SERVER_URL = getEnvironmentVariable("CI_SERVER_URL");
$GITLAB_PROJECT_ID = getEnvironmentVariable("CI_PROJECT_ID");
$COMMIT_ID = getEnvironmentVariable("CI_COMMIT_SHA");

$composer_json = json_decode(file_get_contents(__DIR__ . "/../../../../composer.json"));
$version = $composer_json->version;

$changelog_md = file_get_contents(__DIR__ . "/../../../../CHANGELOG.md");
$changelog_header = "## [" . $version . "]";
$changelog_header_pos = strpos($changelog_md, $changelog_header);
if ($changelog_header_pos === false) {
    die("Changelog for " . $version . " not found!\n");
}
$changelog = substr($changelog_md, $changelog_header_pos + strlen($changelog_header));
$changelog = substr($changelog, 0, strpos($changelog, "\n\n"));
$changelog = trim($changelog);

$tag_name = "v" . $version;

$api_url = $GITLAB_SERVER_URL . "/api/v4/projects/" . $GITLAB_PROJECT_ID . "/repository/tags?tag_name=" . rawurlencode($tag_name) . "&ref=" . rawurlencode($COMMIT_ID) . "&message="
    . rawurlencode($changelog) . "&release_description=" . rawurlencode($changelog);

$curl = null;
$status_code = null;
try {
    $curl = curl_init($api_url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ["PRIVATE-TOKEN: " . $AUTO_VERSION_TAG_TOKEN]);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);
    echo "Response: " . $response . "\n";

    $status_code = intval(curl_getinfo($curl, CURLINFO_HTTP_CODE));
    echo "Status code: " . $status_code . "\n";
} finally {
    if ($curl !== null) {
        curl_close($curl);
    }
}
if ($status_code !== 201) {
    die(1);
}
