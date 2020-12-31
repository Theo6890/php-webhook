<?php
$artifactString = "artifact.zip";
$token = "GitLabAccessToken: API_READ";

function download($url, $fp){
    global $artifactString, $token;
    $fArtifact = fopen("$artifactString", "w+");

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FILE, $fArtifact);
    curl_setopt($ch,CURLOPT_ENCODING, '');   
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("PRIVATE-TOKEN: $token")); // Inject GitLab personal token
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // This will follow any redirects
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    curl_exec($ch); // Execute the cURL statement
    $st_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fArtifact);

    if($st_code == 200) fwrite($fp, "File downloaded successfully!\n");
    else  fwrite($fp, "Error downloading file!\n");

    return $st_code;
}

function extractArtifact($fp){
    global $artifactString;
    $zip = new ZipArchive;
    if (!$zip) {
        fwrite($fp, "Could not make ZipArchive object\n");
        exit;
    }
    $zip->open("$artifactString");
    $zip->extractTo(".");
    $zip->close();
}

//Zip, unzip & php-zip MUST be installed on the server
function updateFiles($path, $fp, $branch) {
    if(getcwd() == $path) {
        $url = "https://gitlab.com/api/v4/projects/20776891/jobs/artifacts/$branch/download?job=app-build";
        fwrite($fp, "$url\n");
        $old = $branch."_old_".".zip";
        //Zip current folder and delete archived files
        exec("zip -x dl.php functions.php .well-known/* cg-bin/* -9 -m -r $old .");
            
        download($url, $fp);     
        extractArtifact($fp);
            
        // Move files in dist/omnia-website to .
        exec("mv dist/omnia-website/* .");
        exec("mv dist/omnia-website/.thizfze .");
        exec("mv $old /home/........");
        exec("rm artifact.zip");
    }
}

?>