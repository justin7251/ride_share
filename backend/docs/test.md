

http GET http://localhost:9001/api/users "Authorization: Bearer 1|RgUzLHXYhqQNJxeCPRMzNkVZZlgvmNlT3Loc92zbb5c592a3"

http POST http://localhost:9001/api/drivers "Authorization: Bearer 1|RgUzLHXYhqQNJxeCPRMzNkVZZlgvmNlT3Loc92zbb5c592a3" name="John Doe" license_number="DL123456" status="active" vehicle_info[year]=2022 vehicle_info[make]="Toyota" vehicle_info[model]="Camry" vehicle_info[color]="Silver" vehicle_info[plate_number]="ABC123"

http POST http://localhost:9001/api/ride "Authorization: Bearer 1|RgUzLHXYhqQNJxeCPRMzNkVZZlgvmNlT3Loc92zbb5c592a3" origin[lat]=40.7128 origin[lng]=-74.0060 destination[lat]=40.7614 destination[lng]=-73.9776 origin_name="Downtown Manhattan" destination_name="Empire State Building" driver_id=1