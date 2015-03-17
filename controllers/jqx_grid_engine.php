<?php

if (!defined('BASEPATH')) {
    exit( 'No direct script access allowed' );
}

class jqx_grid_engine extends CI_Controller {

    public $data;

    public function __construct () {

        parent::__construct();
        $this->load->model("query_manager_model", "query_manager");
    }

    public function index ($query_id = 0) {

        if ($query_id == 0) {
            printf("Need a query id");
            exit;
        }

        $command = $this->input->get_post("command");

        $this->data['start_date'] = $this->input->get_post("start_date") ? $this->input->get_post("start_date") : date("Y-m-d");
        $this->data['end_date'] = $this->input->get_post("end_date") ? $this->input->get_post("end_date") : date("Y-m-d");

        $test = str_replace("-", "", sprintf("%s%s", $this->data['start_date'], $this->data['end_date']));

        if (is_numeric($test) && is_numeric($query_id)) {

            $this->data['query'] = $this->query_manager->execute($query_id, array( 'start_date' => $this->data['start_date'], 'end_date' => $this->data['end_date'] ));

            $this->data['result_array'] = $this->data['query']['result'];
            $this->data['result_header'] = isset( $this->data['result_array'][0] ) ? array_keys($this->data['result_array'][0]) : array();
            $this->data['result'] = array();

            if ($command == "Export") {

                $filename = sprintf("%s-%s.csv", uniqid("Query-Export"), date("Y-m-d"));

                header("Content-type: application/ms-excel");
                header("Content-disposition: attachment; filename={$filename}");

                $fp = fopen("php://output", "w");

                foreach (array_merge(array( $this->data['result_header'] ), $this->data['result_array']) as $line) {
                    fputcsv($fp, $line);
                }

            } else {

                $header = array(
                    'datafields' => array(),
                    'columns' => array(),
                );

                $row = new stdClass();
                $row->{'name'} = "guid";
                $row->{'type'} = "string";

                $header['datafields'][] = $row;
                $columns = array();
                $datafields = array();

                foreach ($this->data['result_header'] as $row) {

                    $column = new stdClass();
                    $column->{'datafield'} = $row;

                    $datafield = new stdClass();
                    $datafield->{'name'} = $row;

                    switch ($row) {
                        /*
                         * FLOATS
                         */
                        case "ecpm":
                            $column->{'text'} = "eCPM";
                            $column->{'width'} = "75";
                            $column->{'cellsformat'} = "F2";
                            $column->{'cellsalign'} = "right";
                            $datafield->{'name'} = $row;
                            $datafield->{'type'} = "float";
                            break;

                        case "revenue_sum":
                        case "margin":
                            $column->{'text'} = $this->format_title($row);
                            $column->{'width'} = "100";
                            $column->{'cellsformat'} = "F2";
                            $column->{'cellsalign'} = "right";
                            $column->{'aggregates'} = array( 'sum' );
                            $datafield->{'name'} = $row;
                            $datafield->{'type'} = "float";
                            break;

                        case "average_sec":
                            $column->{'text'} = "Avg Duration";
                            $column->{'width'} = "75";
                            $column->{'cellsalign'} = "right";
                            $datafield->{'name'} = $row;
                            $datafield->{'type'} = "float";
                            break;

                        /*
                        * INTEGERS
                        */
                        case "drops":
                        case "clicks":
                            $column->{'text'} = $this->format_title($row);
                            $column->{'width'} = "100";
                            $column->{'cellsalign'} = "right";
                            $column->{'aggregates'} = array( 'sum' );
                            $datafield->{'name'} = $row;
                            $datafield->{'type'} = "int";
                            break;

                        case "field_id":
                            $column->{'text'} = "ID";
                            $column->{'width'} = "70";
                            $column->{'cellsalign'} = "right";
                            $datafield->{'name'} = $row;
                            $datafield->{'type'} = "int";
                            break;

                        /*
                         * STRINGS
                         */

                        case "campaign":
                            $column->{'text'} = "Campaign";
                            $column->{'width'} = "200";
                            $column->{'cellsalign'} = "left";
                            $datafield->{'name'} = $row;
                            $datafield->{'type'} = "string";
                            break;

                        default:
                            $column->{'text'} = $this->format_title($row);
                            $column->{'width'} = "75";
                            $datafield->{'type'} = "string";
                            break;
                    }

                    $columns[] = $column;
                    $datafields[] = $datafield;

                }

                $rows = array();

                foreach ($this->data['result_array'] as $row) {
                    $new_row = array();
                    $new_row['guid'] = uniqid();

                    foreach ($row as $key => $value) {
                        $new_row[$key] = $value;
                    }

                    $rows[] = $new_row;
                }

                $this->output->set_content_type('application/json');

                if (count($rows) > 0) {
                    print json_encode(array(
                                          'columns' => $columns,
                                          'datafields' => $datafields,
                                          'results' => $rows,
                                          'sql' => $this->data['query']['sql'],
                                          'error' => $this->data['query']['error']
                                      ));
                } else {
                    print json_encode(array(
                                          'columns' => array(
                                              array(
                                                  "text" => "Result Status",
                                                  "datafield" => "result_status",
                                                  "width" => 400
                                              )
                                          ),
                                          'datafields' => array(
                                              array(
                                                  "name" => "result_status",
                                                  "type" => "string"
                                              )
                                          ),
                                          'results' => array(
                                              array(
                                                  'result_status' => "No Results"
                                              )
                                          ),
                                          'sql' => $this->data['query']['sql'],
                                          'error' => $this->data['query']['error']
                                      ));

                }
            }
        } else {
            printf("sorry, those weren't numeric values");
        }
    }

    function format_title ($title) {

        $parts = explode("_", $title);
        $title = "";

        foreach ($parts as $part) {
            $title .= ucfirst($part) . " ";
        }

        return $title;
    }

}
