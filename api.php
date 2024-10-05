<?php
include 'database.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

error_log("Raw input: " . file_get_contents('php://input'));
error_log("Decoded input: " . print_r($input, true));

switch ($method) {
    case 'GET':
        if (isset($_GET['players_id'])) {
            $players_id = (int) $_GET['players_id'];
            $stmt = $conn->prepare("SELECT * FROM players_data WHERE players_id = ?");
            $stmt->bind_param("i", $players_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            echo json_encode($data);
        } else {
            $result = $conn->query("SELECT * FROM players_data");
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode($users);
        }
        break;

    case 'POST':
        if (!empty($input['players_name']) && !empty($input['players_email']) && isset($input['players_score'])) {
            $players_name = $input['players_name'];
            $players_email = $input['players_email'];
            $players_score = (int) $input['players_score'];

            $stmt = $conn->prepare("INSERT INTO players_data (players_name, players_email, players_score) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $players_name, $players_email, $players_score);

            if ($stmt->execute()) {
                echo json_encode(["message" => "Player added successfully"]);
            } else {
                echo json_encode(["error" => "Failed to add player"]);
            }
        } else {
            echo json_encode(["error" => "Missing required fields"]);
        }
        break;

    case 'PUT':
        if (isset($_GET['players_id']) && !empty($input['players_name']) && !empty($input['players_email']) && isset($input['players_score'])) {
            $players_id = (int) $_GET['players_id'];
            $players_name = $input['players_name'];
            $players_email = $input['players_email'];
            $players_score = (int) $input['players_score'];
    
            $stmt = $conn->prepare("UPDATE players_data SET players_name = ?, players_email = ?, players_score = ? WHERE players_id = ?");
            $stmt->bind_param("ssii", $players_name, $players_email, $players_score, $players_id);
    
            if ($stmt->execute()) {
                echo json_encode(["message" => "Player updated successfully"]);
            } else {
                echo json_encode(["error" => "Failed to update player"]);
            }
            } else {
                echo json_encode(["error" => "Missing or invalid required fields"]);
            }
            break;    

    case 'PATCH':
        if (isset($_GET['players_id'])) {
            $players_id = (int) $_GET['players_id'];
        
            $updateFields = [];
            $params = [];
            $types = '';
        
            if (!empty($input['players_name'])) {
                $updateFields[] = "players_name = ?";
                $params[] = &$input['players_name'];
                $types .= 's';
                }
                if (!empty($input['players_email'])) {
                    $updateFields[] = "players_email = ?";
                    $params[] = &$input['players_email'];
                    $types .= 's';
                    }
                    if (isset($input['players_score'])) {
                        $updateFields[] = "players_score = ?";
                        $params[] = &$input['players_score'];
                        $types .= 'i';
                    }
        
                    if (!empty($updateFields)) {
                        $query = "UPDATE players_data SET " . implode(", ", $updateFields) . " WHERE players_id = ?";
                        $stmt = $conn->prepare($query);
        
                        $params[] = &$players_id;
                        $types .= 'i';
        
                        $stmt->bind_param($types, ...$params);
        
                        if ($stmt->execute()) {
                            echo json_encode(["message" => "Player updated partially"]);
                        } else {
                            echo json_encode(["error" => "Failed to update player"]);
                        }
                    } else {
                        echo json_encode(["error" => "No valid fields to update"]);
                    }
                } else {
                    echo json_encode(["error" => "Missing players_id"]);
                }
                break;
    case 'DELETE':
        if (isset($_GET['players_id'])) {
            $players_id = (int) $_GET['players_id'];
            
            $stmt = $conn->prepare("DELETE FROM players_data WHERE players_id = ?");
            $stmt->bind_param("i", $players_id);
            
                if ($stmt->execute()) {
                    echo json_encode(["message" => "Player deleted successfully"]);
                    } else {
                        echo json_encode(["error" => "Failed to delete player"]);
                    }
                    } else {
                        echo json_encode(["error" => "Missing players_id"]);
                    }
                    break;
            
                default:
                    echo json_encode(["error" => "Invalid request method"]);
                    break;
            }
$conn->close();