-- Seeds para ambiente de testes
USE codechat_manager;

INSERT INTO users (name, email, password)
VALUES ('Admin', 'admin@codechat.local', '$2y$12$TAuYVJyQmqc3T8Xsci/BJOQXo6A0APU2R7GTikX0WSoWUHzRDwsWS');

INSERT INTO instances (instance_name, description, api_key, webhook_url, status)
VALUES
  ('suporte', 'Atendimento suporte', NULL, NULL, 'disconnected'),
  ('vendas', 'Equipe comercial', NULL, NULL, 'pending');
