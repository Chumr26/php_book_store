
import os

def merge():
    # Read original
    with open('db/bookstore.sql', 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Read seed
    with open('db/seed_output.sql', 'r', encoding='utf-8') as f:
        seed_content = f.read()

    # Define markers
    start_marker = "DELIMITER $$"
    # The first delimiter block is sp_seed_bulk_orders.
    # The block ends with DROP PROCEDURE sp_seed_reviews;
    # Let's find the start index of the first procedure and the end of the last drop.
    
    # Looking at the file content in memory:
    # 504: DELIMITER $$
    # ...
    # 705: DROP PROCEDURE sp_seed_reviews;
    
    # We want to replace from line 504 (approx) to 706.
    # We should search for the specific comment block before it to be safe?
    # "-- Bulk generate orders + order details for analytics"
    
    start_str = "-- Bulk generate orders + order details for analytics"
    end_str = "DROP PROCEDURE sp_seed_reviews;"
    
    start_idx = content.find(start_str)
    if start_idx == -1:
        print("Error: Start marker not found")
        return

    # We want to keep the start_str comment or replace it? 
    # Let's replace the content AFTER the start string line.
    # Find the newline after start_str
    insert_point = content.find('\n', start_idx) + 1
    
    end_idx = content.find(end_str)
    if end_idx == -1:
        print("Error: End marker not found")
        return
        
    end_point = end_idx + len(end_str)
    
    # Verify what we are cutting
    cut_content = content[insert_point:end_point]
    # print("Cutting:", cut_content[:100], "...", cut_content[-100:])
    
    new_content = content[:insert_point] + "\n" + seed_content + "\n" + content[end_point:]
    
    with open('db/bookstore.sql', 'w', encoding='utf-8') as f:
        f.write(new_content)
        
    print("Successfully merged SQL files.")

if __name__ == "__main__":
    merge()
