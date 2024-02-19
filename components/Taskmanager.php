<?php
    namespace components;

    abstract class Taskmanager
    {
        public function getTask()
        {
            $answ = "
                        SELECT
                                task.id         as task_id,
                                task.task       as task_name
                         FROM
                                task            as task
                                
                        WHERE( shop_id = ".GROUP_ID.")        
                    ";
            $answ =  mysqlQuery($answ); 
            if(mysqli_num_rows($answ) != 0) return true;
            return false;
        }

        public function setTask($task)
        {
            $answ = "INSERT INTO task(id, task, shop_id) VALUES(NULL, '".$task."', ".GROUP_ID.")";
            $answ =  mysqlQuery($answ);
            return;
        }
        public function deleteTask($task)
        {
            $answ = "DELETE FROM task WHERE (task = '".$task."' AND shop_id = ".GROUP_ID.")";
            $answ =  mysqlQuery($answ); 
            return;
        }
        public function deleteAllTasks()
        {
            $answ = "DELETE FROM task WHERE (shop_id = ".GROUP_ID.")";
            $answ =  mysqlQuery($answ); 
            return;
        }
    }