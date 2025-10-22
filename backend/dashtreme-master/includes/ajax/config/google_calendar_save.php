<?php
// Endpoint desativado. A integração com Google Calendar foi removida.
http_response_code(404);
header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Rota indisponível']);
