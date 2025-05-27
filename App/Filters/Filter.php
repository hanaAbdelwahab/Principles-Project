<!-- App/Filters/Filter.php-->
<?php
interface Filter {
    public function apply(string &$sql, array &$params): void;
}
?>