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
 * @param int    $expect_status_code
 * @param bool   $post
 *
 * @return string|null
 */
function gitlabRequest(string $api_url, int $expect_status_code, bool $post = false)/* : ?string*/
{
    $AUTO_VERSION_TAG_TOKEN = getEnvironmentVariable("AUTO_VERSION_TAG_TOKEN");
    $SERVER_URL = getEnvironmentVariable("CI_SERVER_URL");
    $PROJECT_ID = getEnvironmentVariable("CI_PROJECT_ID");

    $curl = null;
    $response = null;
    $status_code = null;
    try {
        $request_url = $SERVER_URL . "/api/v4/projects/" . $PROJECT_ID . "/" . $api_url;
        echo "Request url: " . $request_url . "\n";

        $curl = curl_init($request_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ["PRIVATE-TOKEN: " . $AUTO_VERSION_TAG_TOKEN]);

        if ($post) {
            curl_setopt($curl, CURLOPT_POST, true);
        }

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
    if ($status_code !== $expect_status_code) {
        echo "Expect status code: " . $expect_status_code . "\n";
        die(1);
    }

    return $response;
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

$maintainer_user_id = gitlabRequest("members", 200);
if (empty($maintainer_user_id) || empty($maintainer_user_id = json_decode($maintainer_user_id, true)) || !is_array($maintainer_user_id)) {
    echo "No project members found!\n";
    die(1);
}
$maintainer_user_id = array_filter($maintainer_user_id, function (array $member) : bool {
    return ($member["access_level"] === 40);
});
if (empty($maintainer_user_id)) {
    echo "No project maintainer found!\n";
    die(1);
}
$maintainer_user_id = current($maintainer_user_id)["id"];

gitlabRequest("repository/tags?tag_name=" . rawurlencode("v" . $version) . "&ref=" . rawurlencode($COMMIT_ID) . "&message=" . rawurlencode($changelog) . "&release_description="
    . rawurlencode($changelog), 201, true);

gitlabRequest("merge_requests?source_branch=" . rawurlencode("develop") . "&target_branch=" . rawurlencode("master") . "&title=" . rawurlencode("WIP: Develop") . "&assignee_id="
    . rawurlencode($maintainer_user_id), 201, true);
