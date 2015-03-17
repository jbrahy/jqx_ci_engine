<?php

class Query_manager_model extends CI_model {

    function __construct () {

        parent::__construct();
    }

    function execute ($query_id, $parameters = array()) {

        $this->db->where('query_id', $query_id);
        $queries_query = $this->db->get('queries');
        $query = $queries_query->row_array();
        $sql = $query['query_sql'];

        if (count($parameters) > 0) {
            foreach ($parameters as $key => $value) {
                $key_pattern = sprintf("%%%%%s%%%%", strtoupper($key));
                $sql = str_ireplace($key_pattern, $value, $sql);
            }
        }

        $result = $this->db->query($sql);

        if ($result) {
            return array(
                'sql' => $this->db->last_query(),
                'error' => $this->db->_error_message(),
                'result' => $result->result_array()
            );
        } else {
            printf("<pre>Error: %s\nSQL: %s\n</pre>", $this->db->_error_message(), $this->db->last_query());
        }
    }
}
