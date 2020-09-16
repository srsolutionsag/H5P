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

/**
 * @param string $api_url
 */
function gitlabRequest(string $api_url)/* : void*/
{
    $AUTO_VERSION_TAG_TOKEN = getEnvironmentVariable("AUTO_VERSION_TAG_TOKEN");
    $SERVER_URL = getEnvironmentVariable("CI_SERVER_URL");
    $PROJECT_ID = getEnvironmentVariable("CI_PROJECT_ID");

    $curl = null;
    $status_code = null;
    try {
        $curl = curl_init($SERVER_URL . "/api/v4/projects/" . $PROJECT_ID . "/" . $api_url);
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
}

$COMMIT_ID = getEnvironmentVariable("CI_COMMIT_SHA");
$USER_ID = getEnvironmentVariable("GITLAB_USER_ID");

$composer_json = json_decode(file_get_contents(getcwd() . "/composer.json"));
$version = $composer_json->version;

$changelog_md = file_get_contents(getcwd() . "/CHANGELOG.md");
$changelog_header = "## [" . $version . "]";
$changelog_header_pos = strpos($changelog_md, $changelog_header);
if ($changelog_header_pos === false) {
    echo "Changelog for " . $version . " not found!\n";
    die(1);
}
$changelog = substr($changelog_md, $changelog_header_pos + strlen($changelog_header));
$changelog = substr($changelog, 0, strpos($changelog, "\n\n"));
$changelog = trim($changelog);

gitlabRequest("repository/tags?tag_name=" . rawurlencode("v" . $version) . "&ref=" . rawurlencode($COMMIT_ID) . "&message=" . rawurlencode($changelog) . "&release_description="
    . rawurlencode($changelog));

gitlabRequest("merge_requests?source_branch=" . rawurlencode("develop") . "&target_branch=" . rawurlencode("master") . "&title=" . rawurlencode("WIP: Develop") . "&assignee_id="
    . rawurlencode($USER_ID));
