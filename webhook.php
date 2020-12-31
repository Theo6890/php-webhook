<?php

include 'functions.php';

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'));

$branch = $data->object_attributes->ref;
$status = $data->object_attributes->status;
$jobId = $data->builds[0]->id;
$stage = $data->object_attributes->stages[0];

if($status == "success"){
    $output_to_file = "stage: ".$stages."\njob id: ".$jobId;

    $fp = fopen("logs.txt", "w");
    $now = date("Y-m-d___H-i-s");
    fwrite($fp, "$now\n".$output_to_file."\n");


    if($branch == "development"){
        $path = '/home/................';
    }
    else if($branch == "master") $path  = '/home/................';

    chdir($path);

    //Need to take last job from master or development & pass it to rename old
    //Zip, unzip & php-zip MUST be installed on the server
    updateFiles($path, $fp, $branch);

    fclose($fp);
}

?>
