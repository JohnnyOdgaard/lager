-- Tilføj 'opladning_fra' felt til opladning tabellen
ALTER TABLE opladning 
ADD COLUMN opladning_fra VARCHAR(100) NULL AFTER kwh;
