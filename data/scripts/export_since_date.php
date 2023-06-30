<?php
/**
 * Perform an export since specific date and resource.
 */
require dirname(__DIR__, 4) . '/bootstrap.php';

$application = Omeka\Mvc\Application::init(require OMEKA_PATH . '/application/config/application.config.php');
$serviceLocator = $application->getServiceManager();
$connection = $serviceLocator->get('Omeka\Connection');

$options = getopt(null, ['help', 'resource-type:', 'since-date:']);
$entitiesMap = [
    'item_sets' => 'Omeka\\Entity\\ItemSet',
    'items' => 'Omeka\\Entity\\Item',
    'media' => 'Omeka\\Entity\\Media',
];

function help()
{
    return <<<'HELP'

    export_since_date --resource-type RESOURCE_TYPE --since-date DATE
    export_since_date --help

    Options:
    --resource-type RESOURCE_TYPE
        Required. Choose which resource type will be exported ('item_sets', 'items' or 'media')

    --since-date DATE
        Required. Set the date from which you wish to export (e.g. '2023-06-21')

    --help
        Display this help

    HELP;
}

function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

if (isset($options['help'])) {
    echo help();
    exit;
}

if (!isset($options['resource-type'])) {
    fprintf(STDERR, "No resource type given ; use --resource-type <resourceType> ('item_sets', 'items' or 'media')\n");
    echo help();
    exit(1);
}

if (!array_key_exists($options['resource-type'], $entitiesMap)) {
    fprintf(STDERR, "Resource not supported ; set one of this values: 'item_sets', 'items' or 'media'\n");
    exit(1);
}

if (!isset($options['since-date'])) {
    fprintf(STDERR, "No date given; use --since-date <date> (e.g. '2023-06-21')\n");
    echo help();
    exit(1);
}

if (!(validateDate($options['since-date']))) {
    fprintf(STDERR, "Date does not match 'Y-m-d' format or is not valid\n");
    exit(1);
}

$resourceType = $options['resource-type'];
$date = $options['since-date'];
$targetEntity = $entitiesMap[$resourceType];

$query = "SELECT id FROM necropolis_resource WHERE deleted >= ? AND resource_type = ?";

try {
    $resultSet = $connection->executeQuery($query, [$date, $targetEntity]);
    $deletedIds = $resultSet->fetchAll(\PDO::FETCH_COLUMN, 0);

    foreach ($deletedIds as $id) {
        fprintf(STDOUT, "$id\n");
    }
} catch (\Exception $e) {
    fprintf(STDERR, "Error: %s\n", $e->getMessage());
}
