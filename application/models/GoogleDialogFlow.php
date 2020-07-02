<?php
class GoogleDialogFlow extends CI_model
    {
        var $agent_id = '255556ef-ead7-4579-b726-2eac5feaab48';
        function bot()
            {
                $sx = '
                <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
                <df-messenger
                intent="WELCOME"
                chat-title="BrapciAjuda"
                agent-id="'.$this->agent_id.'"
                language-code="pt-br"
                ></df-messenger>';
                return($sx);
            }
    }