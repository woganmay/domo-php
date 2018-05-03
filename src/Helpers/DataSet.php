<?php

namespace WoganMay\DomoPHP\Helpers;

class DataSet
{
    /**
     * Create a new dataset based on a CSV file.
     *
     * @param string   $name   DataSet Name
     * @param resource $file   The path to the file
     * @return mixed
     * @throws \Exception
     */
    public function createDataSet($name, $file)
    {
        if (!file_exists($file)) {
            throw new \Exception('File not found!');
        }

        $handle = fopen($file, 'r');

        $headers = [];
        $columns = [];
        $csv = '';

        if ($handle) {
            $pos = 0;

            while (($data = fgetcsv($handle)) !== false) {

                // Process $line

                if ($pos == 0) {
                    $headers = $data;
                } else {
                    if ($pos == 1) {
                        $columns = $this->inferSchema($headers, $data);
                    }

                    // Process the rest of the CSV
                    $csv .= '"'.implode('","', $data).'"'."\n";
                }

                $pos++;
            }

            fclose($handle);

            // Create the dataset
            $dataSet = $this->API->DataSet->create($name, $columns);

            // Update it
            $populate = $this->API->DataSet->import($dataSet->id, $csv);

            return $dataSet;
        } else {
            throw new \Exception('Error reading from the file');
        }
    }
}