Update audit set brdr = '1000', brcr = '1000';
update branch set branch = '1000';
update fixassets set branch = '1000';
update glmast set branch = '1000';
update invhead set branch = '1000';
update p_ohead set branch = '1000';
update trmain set branch = '1000',br = '1000';


update audit set brdr = brdr + 1000;
update audit set brcr = brcr + 1000;
update fixassets set branch = branch + 1000;
update glmast set branch = branch + 1000;
update invhead set branch = branch + 1000;
update p_ohead set branch = branch + 1000;
update trmain set branch = branch + 1000,br = br + 1000;



update dockets set truckbranch = truckbranch + 1000,trailerbranch = trailerbranch + 1000;
update costheader set truckbranch = truckbranch + 1000,trailerbranch = trailerbranch + 1000;
update vehicles set branch = branch + 1000;