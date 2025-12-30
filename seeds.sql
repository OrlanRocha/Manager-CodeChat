-- Seeds para ambiente de testes
USE codechat_manager;

INSERT INTO users (name, email, password, role, status)
VALUES ('Admin', 'admin@codechat.local', '$2y$12$TAuYVJyQmqc3T8Xsci/BJOQXo6A0APU2R7GTikX0WSoWUHzRDwsWS', 'admin', 'active');

INSERT INTO instances (user_id, instance_name, description, api_key, webhook_url, status)
VALUES
  (1, 'suporte', 'Atendimento suporte', NULL, NULL, 'disconnected'),
  (1, 'vendas', 'Equipe comercial', NULL, NULL, 'pending');
