-- Tilføj 'fra_kwh' felt til opladning tabellen
-- Dette felt bruges til at angive hvor mange kWh der var på batteriet inden opladningen
ALTER TABLE opladning 
ADD COLUMN fra_kwh DECIMAL(10,2) NULL AFTER kwh;
