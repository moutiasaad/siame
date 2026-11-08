-- Données de démonstration (mots de passe : Admin@2026 / Operateur@2026)
USE siame;

INSERT INTO users (first_name, last_name, email, password_hash, role) VALUES
('Moutia', 'Bensaad', 'admin@siame.tn',     '$2y$12$oRopCZyxrbOrNm8QSeFoAu5aiSDHVZUDSxf9SSseaThw3012TPX1.', 'admin'),
('Opérateur', 'Atelier', 'operateur@siame.tn', '$2y$12$y23pkTaxoc3ja7f9fbJhb.bhx04LzixcnW/E3yt9y3LhTk9A6/jnq', 'operator');

INSERT INTO machines (name, model, location, state, temp_max) VALUES
('Presse H2000 — Ligne 1', 'Billion Hercule H2000', 'Atelier injection · Ligne 1', 'on', 60.0),
('Presse H1300 — Ligne 2', 'Billion Hercule H1300', 'Atelier injection · Ligne 2', 'on', 60.0),
('Presse H2000 — Ligne 3', 'Billion Hercule H2000', 'Atelier injection · Ligne 3', 'off', 60.0);
