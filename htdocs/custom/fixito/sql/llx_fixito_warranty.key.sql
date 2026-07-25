ALTER TABLE llx_fixito_warranty ADD UNIQUE INDEX uk_fixito_warranty_ref (ref, entity);
ALTER TABLE llx_fixito_warranty ADD INDEX idx_fixito_warranty_soc (fk_soc);
ALTER TABLE llx_fixito_warranty ADD INDEX idx_fixito_warranty_serial (serial_number);
ALTER TABLE llx_fixito_warranty ADD INDEX idx_fixito_warranty_end (date_warranty_end);
