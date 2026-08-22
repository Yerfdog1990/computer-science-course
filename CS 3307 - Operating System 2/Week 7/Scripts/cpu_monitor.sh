#!/bin/bash

# Output file
OUTPUT_FILE="cpu_usage_log.txt"

# Write header
echo "CPU Usage Monitoring Log" > "$OUTPUT_FILE"
echo "Started: $(date)" >> "$OUTPUT_FILE"
echo "=========================================" >> "$OUTPUT_FILE"

# Monitor CPU usage every minute for 5 minutes
for i in {1..5}
do
    echo "Sample $i - $(date)" >> "$OUTPUT_FILE"

    # Display CPU usage summary
    top -l 1 | grep "CPU usage" >> "$OUTPUT_FILE"

    echo "-----------------------------------------" >> "$OUTPUT_FILE"

    # Wait one minute before the next sample
    if [ $i -lt 5 ]; then
        sleep 60
    fi
done

echo "Monitoring completed."
echo "Results saved to $OUTPUT_FILE"
