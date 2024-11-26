/* Bank Table */

DROP TABLE IF EXISTS bank;
CREATE TABLE bank(
	bank_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	bank_name VARCHAR(100) NOT NULL,
    bank_identifier_code VARCHAR(100) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX bank_index_bank_id ON bank(bank_id);

INSERT INTO bank (bank_name, bank_identifier_code, last_log_by) VALUES
('Banco de Oro (BDO)', '010530667', 1),
('Metrobank', '010269996', 1),
('Land Bank of the Philippines', '010350025', 1),
('Bank of the Philippine Islands (BPI)', '010040018', 1),
('Philippine National Bank (PNB)', '010080010', 1),
('Security Bank', '010140015', 1),
('UnionBank of the Philippines', '010419995', 1),
('Development Bank of the Philippines (DBP)', '010590018', 1),
('EastWest Bank', '010620014', 1),
('China Banking Corporation (Chinabank)', '010100013', 1),
('RCBC (Rizal Commercial Banking Corporation)', '010280014', 1),
('Maybank Philippines', '010220016', 1),
('Bank of America', 'BOFAUS3N', 1),
('JPMorgan Chase', 'CHASUS33', 1),
('Wells Fargo', 'WFBIUS6W', 1),
('Citibank', 'CITIUS33', 1),
('U.S. Bank', 'USBKUS44', 1),
('Bank of New York Mellon', 'BKONYUS33', 1),
('State Street Corporation', 'SSTTUS33', 1),
('Goldman Sachs', 'GOLDUS33', 1),
('Morgan Stanley', 'MSNYUS33', 1),
('Capital One', 'COWNUS33', 1),
('PNC Financial Services Group', 'PNCCUS33', 1),
('Truist Financial Corporation', 'TRUIUS33', 1),
('Charles Schwab Corporation', 'SCHWUS33', 1),
('Ally Financial', 'ALLYUS33', 1),
('TD Bank', 'TDUSUS33', 1),
('Fifth Third Bank', 'FTBCUS3J', 1),
('KeyBank', 'KEYBUS33', 1),
('Huntington Bancshares', 'HBANUS33', 1),
('Regions Financial Corporation', 'RGNSUS33', 1),
('M&T Bank', 'MANTUS33', 1),
('SunTrust Banks', 'STBAUS33', 1),
('BB&T Corporation', 'BBTUS33', 1),
('Emirates NBD', 'EBILAEAD', 1),
('First Abu Dhabi Bank', 'NBADAEAAXXX', 1),
('Abu Dhabi Commercial Bank', 'ADCBAEAAXXX', 1),
('Dubai Islamic Bank', 'DIBAEAAXXX', 1),
('Mashreq Bank', 'BOMLAEAD', 1),
('Union National Bank', 'UNBAEAAXXX', 1),
('Rakbank', 'RAKAEAAXXX', 1),
('Commercial Bank of Dubai', 'CBDAEAAXXX', 1),
('Emirates Islamic Bank', 'EIILAEAD', 1),
('Ajman Bank', 'AJBLAEAD', 1),
('Sharjah Islamic Bank', 'SIBAEAAXXX', 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */